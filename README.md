# PaymentSaferpayBundle

a simple inofficial implementation of the saferpay payment service as a symfony bundle

## installation

### composer.json

    {
        "require": {
            "payment/saferpay-bundle": "master",
            "payment/saferpay-guzzle": "master"
        }
    }

### app/AppKernel.php

    new Payment\Bundle\SaferpayBundle\PaymentSaferpayBundle(),

### app/config/config.yml

    payment_saferpay:
        defaults:
            init:
                DESCRIPTION: symfony sample implementation

## usage

    /** @var Symfony\Component\HttpFoundation\Session\SessionInterface $session  */
    $session = $this->container->get('session');

    /** @var Symfony\Component\Routing\Generator\UrlGeneratorInterface $router  */
    $router = $this->container->get('router');

    /** @var Payment\Saferpay\Saferpay $saferpay */
    $saferpay = $this->container->get('payment.saferpay');

    // set http client
    $saferpay->setHttpClient(new Payment\Saferpay\Http\Client\GuzzleClient\GuzzleClient());

    // set data
    $saferpay->setData($session->get('payment.saferpay.data'));

    // check if we come from saferpay and the call was a success
    if($this->getRequest()->query->get('status') == 'success')
    {
        if($saferpay->confirmPayment($this->getRequest()->query->get('DATA'), $this->getRequest()->query->get('SIGNATURE')) != '')
        {
            if($saferpay->completePayment() != '')
            {
                $session->remove('payment.saferpay.data');
            }
        }
    }
    else
    {
        $url = $saferpay->initPayment(new Payment\Saferpay\SaferpayKeyValue(array(
            'AMOUNT' => 10250,
            'DESCRIPTION' => sprintf('Bestellnummer: %s', '000001'),
            'ORDERID' => '000001',
            'SUCCESSLINK' => $router->generate('route_name', array('status' => 'success'), Router::ABSOLUTE_URL),
            'FAILLINK' => $router->generate('route_name', array('status' => 'fail'), Router::ABSOLUTE_URL),
            'BACKLINK' => $router->generate('route_name', array(), Router::ABSOLUTE_URL),
            'GENDER' => 'm',
            'FIRSTNAME' => 'Hans',
            'LASTNAME' => 'Muster',
            'STREET' => 'Musterstrasse 300',
            'ZIP' => '0000',
            'CITY' => 'Musterort',
            'COUNTRY' => 'CH',
            'EMAIL' => 'test@test.ch'
        )));

        // assign the data to the session
        $session->set('payment.saferpay.data', $saferpay->getData());

        if($url != '')
        {
            // redirect to saferpay
            return new RedirectResponse($url, 302);
        }
    }