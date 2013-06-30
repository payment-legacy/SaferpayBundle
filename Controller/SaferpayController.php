<?php

namespace Payment\Bundle\SaferpayBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Payment\Bundle\SaferpayBundle\PayInitParameter\PayInitParameterFactory;
use Payment\Saferpay\Saferpay;
use Payment\Saferpay\Data\PayConfirmParameterInterface;
use Payment\Saferpay\Data\PayInitParameterInterface;
use Payment\Saferpay\Data\PayInitParameterWithDataInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class SaferpayController
{
    /**
     * @param bool $doCompletePayment
     * @return PaymentFinishedResponse|RedirectResponse
     */
    protected function pay($doCompletePayment = true) // if complete payment should be done, not only amount reservation
    {
        /* @var Saferpay $saferpay */
        $saferpay = $this->getContainer()->get('payment.saferpay');
        $payInitParameter = $this->getPayInitParameter(true);

        $request = $this->getContainer()->get('request'); // in symfony you will have $this->getRequest() in your controller
        switch($request->query->get('status')){
            case PaymentFinishedResponse::STATUS_OK:
                try {
                    $payConfirmParameter = $saferpay->verifyPayConfirm($request->query->get('DATA'), $request->query->get('SIGNATURE'));

                    if(true === $this->validatePayConfirmParameter($payConfirmParameter, $payInitParameter)){
                        if(false === $doCompletePayment){
                            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_COMPLETION);
                        }

                        $payCompleteResponse = $saferpay->payCompleteV2($payConfirmParameter, 'Settlement');
                        if($payCompleteResponse->getResult() != '0'){
                            return new PaymentFinishedResponse();
                        }

                        return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_COMPLETION);
                    }

                    $saferpay->payCompleteV2($payConfirmParameter, 'Cancel');
                    return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_VALIDATION);
                }catch(\Exception $e){
                    return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_VALIDATION);
                }
                break;
            case PaymentFinishedResponse::STATUS_ERROR:
                return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, $request->query->get('error'));
                break;
        }

        try {
            if($url = $saferpay->createPayInit($payInitParameter)){
                return new RedirectResponse($url);
            }
            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, 'connectionerror');
        }catch(\Exception $e){
            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, 'connectionerror');
        }
    }

    /**
     * @param PayConfirmParameterInterface $payConfirmParameter
     * @param PayInitParameterInterface $payInitParameter
     * @return bool
     */
    protected function validatePayConfirmParameter(PayConfirmParameterInterface $payConfirmParameter, PayInitParameterInterface $payInitParameter)
    {
        return $payConfirmParameter->getAmount() == $payInitParameter->getAmount() && $payConfirmParameter->getCurrency() == $payInitParameter->getCurrency();
    }

    /**
     * @param bool $testMode
     * @return PayInitParameterWithDataInterface
     */
    protected function getPayInitParameter($testMode = false)
    {
        /* @var PayInitParameterFactory $payInitFactory */
        $payInitFactory = $this->getContainer()->get('payment.saferpay.payinitparameter.factory');

        $router = $this->getContainer()->get('router');

        $payInitParameter = $payInitFactory->createPayInitParameter();

        $providerSet = null;

        if(true === $testMode){
            $payInitParameter->setAccountid('99867-94913159');
            $payInitParameter->setPaymentmethods($payInitParameter::PAYMENTMETHOD_SAFERPAY_TESTCARD);
        }else{
            $payInitParameter->setPaymentmethods(array($payInitParameter::PAYMENTMETHOD_MASTERCARD, $payInitParameter::PAYMENTMETHOD_VISA));
        }

        $payInitParameter
            ->setAmount(55050) // 550.50
            ->setDescription(sprintf('Order %s', 1))
            ->setOrderid(1)
            ->setSuccesslink($router->generate('saferpay_payment', array('status' => PaymentFinishedResponse::STATUS_OK), true))
            ->setFaillink($router->generate('saferpay_payment', array('status' => PaymentFinishedResponse::STATUS_ERROR, 'error' => 'fail'), true))
            ->setBacklink($router->generate('saferpay_payment', array('status' => PaymentFinishedResponse::STATUS_ERROR, 'error' => 'back'), true))
            ->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setStreet('Street')
            ->setZip('8000')
            ->setCity('City')
            ->setCountry('CH')
            ->setEmail('john.doe@example.com')
            ->setCurrency('CHF')
            ->setGender($payInitParameter::GENDER_MALE)
        ;

        return $payInitParameter;
    }

    /**
     * @return ContainerInterface
     */
    abstract protected function getContainer();
}