# PaymentSaferpayBundle

A simple inofficial implementation of the saferpay payment service as a symfony bundle.

[![Build Status](https://api.travis-ci.org/payment/SaferpayBundle.png?branch=master)](https://travis-ci.org/payment/SaferpayBundle)
[![Total Downloads](https://poser.pugx.org/payment/saferpay-bundle/downloads.png)](https://packagist.org/packages/payment/saferpay-bundle)
[![Latest Stable Version](https://poser.pugx.org/payment/saferpay-bundle/v/stable.png)](https://packagist.org/packages/payment/saferpay-bundle)
[![Latest Unstable Version](https://poser.pugx.org/payment/saferpay-bundle/v/unstable.png)](https://packagist.org/packages/payment/saferpay-bundle)

## installation

### composer.json

    {
        "require": {
            "payment/saferpay-bundle": "2.*"
        }
    }

### app/AppKernel.php

    new Payment\Bundle\SaferpayBundle\PaymentSaferpayBundle(),

### app/config/config.yml (Full config dump - no need to setup this, default values are set)

    payment_saferpay:
        logger:
            serviceid:            logger
        httpclient:
            serviceid:            payment.saferpay.httpclient.buzz
        payinitparameter:
            serviceid:            payment.saferpay.payinitparameter.default
            data:
                accountid:            ~ # insert here your accountid (test-account: 99867-94913159)
                amount:               ~
                currency:             ~
                description:          ~
                orderid:              ~
                vtconfig:             ~
                successlink:          ~
                faillink:             ~
                backlink:             ~
                notifyurl:            ~
                autoclose:            ~
                ccname:               ~
                notifyaddress:        ~
                usernotify:           ~
                langid:               ~
                showlanguages:        ~
                paymentmethods:       ~
                duration:             ~
                cardrefid:            ~
                delivery:             ~
                appearance:           ~
                address:              ~
                company:              ~
                gender:               ~
                firstname:            ~
                lastname:             ~
                street:               ~
                zip:                  ~
                city:                 ~
                country:              ~
                email:                ~
                phone:                ~

## Usage in your controller
	        <?php        namespace Ticketpark\Bundle\PaymentBundle\Controller;        use Payment\Bundle\SaferpayBundle\Controller\PaymentFinishedResponse;    use Symfony\Bundle\FrameworkBundle\Controller\Controller;    use Payment\Saferpay\Data\PayInitParameterInterface;    use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;        class SaferpayController extends Controller    {        public function payAction(Request $request)        {            $router = $this->get('router');            $route  = $request->get('_route');                $parameters = array(                'amount'        => 50000,                'currency'      => 'CHF',                'description'   => 'Order 1',                'orderid'       => 1865,                'successlink'   => $router->generate($route, array('status' => PaymentFinishedResponse::STATUS_OK), true),                'faillink'      => $router->generate($route, array('status' => PaymentFinishedResponse::STATUS_ERROR, 'error' => 'fail'), true),                'backlink'      => $router->generate($route, array('status' => PaymentFinishedResponse::STATUS_ERROR, 'error' => 'back'), true), 'firstname'     => 'Firstname',                'lastname'      => 'Lastname',                'street'        => 'Street',                'zip'           => '8000',                'city'          => 'City',                'country'       => 'CH',                'email'         => 'john.doe@example.com',                'gender'        => PayInitParameterInterface::GENDER_MALE            );                $payment = $this->get('payment.saferpay')                ->setParameters($parameters)                ->pay(true);                if ($payment instanceof PaymentFinishedResponse){                if ('error' == $payment->getStatus()) {                    throw new BadRequestHttpException(sprintf('Payment failed with error: %s', $payment->getErrorCode()));                }elseif ('ok' == $payment->getStatus()) {                        //Payment has been sucessful.                    //Return response of your choice.                    }            }                return $payment;        }    }
