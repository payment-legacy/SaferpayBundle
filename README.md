# PaymentSaferpayBundle

a simple inofficial implementation of the saferpay payment service as a symfony bundle

## installation

### composer.json

    {
        "require": {
            "payment/saferpay-bundle": "master"
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

### controller

    <?php

    namespace Payment\Bundle\SaferpayTestBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

    use Payment\Saferpay\Saferpay;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Session\SessionInterface;
    use Symfony\Component\Routing\Router;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class DefaultController extends Controller
    {
        /**
         * @Route("/", name="index")
         */
        public function indexAction()
        {
            /** @var SessionInterface $session  */
            $session = $this->container->get('session');

            /** @var UrlGeneratorInterface $router  */
            $router = $this->container->get('router');

            /** @var Saferpay $saferpay */
            $saferpay = $this->container->get('payment.saferpay');

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
                $url = $saferpay->initPayment($saferpay->getKeyValuePrototype()->all(array(
                    'AMOUNT' => 10250,
                    'DESCRIPTION' => sprintf('Bestellnummer: %s', '000001'),
                    'ORDERID' => '000001',
                    'SUCCESSLINK' => $router->generate('index', array('status' => 'success'), Router::ABSOLUTE_URL),
                    'FAILLINK' => $router->generate('index', array('status' => 'fail'), Router::ABSOLUTE_URL),
                    'BACKLINK' => $router->generate('index', array(), Router::ABSOLUTE_URL),
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

            self::printData($saferpay, true);
        }

        protected static function printData($data, $die = false)
        {
            print '<pre>'; print_r($data); print '<pre>';
            if($die) { die(); }
        }
    }

### httpclient

    $saferpay->setHttpClient(new Payment\HttpClient\BuzzClient());

### logger

#### 2.1

    $saferpay->setLogger(new Payment\Bundle\SaferpayBundle\Logger\LoggerBridge($this->container->get('logger')));

#### 2.2

    $saferpay->setLogger($this->container->get('logger'));