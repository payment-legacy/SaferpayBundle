<?php

namespace Payment\Bundle\SaferpayBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Payment\Bundle\SaferpayBundle\PayInitParameter\PayInitParameterFactory;
use Payment\Saferpay\Saferpay;
use Payment\Saferpay\Data\PayConfirmParameterInterface;
use Payment\Saferpay\Data\PayInitParameterInterface;
use Payment\Saferpay\Data\PayInitParameterWithDataInterface;
use Payment\Bundle\SaferpayBundle\Controller\PaymentFinishedResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class SaferpayController
{
    protected $initParameters = array();

    /**
     * Set init parameters
     *
     * See Payment\Saferpay\Data for available parameters.
     *
     * Example:
     * array(
     *   'amount'   => 5000,
     *   'currency' => 'CHF'
     * );
     * will call the methods setAmount(5000) and setCurrency('CHF').
     *
     * @param array $initParameters
     */
    public function setParameters($initParameters = array())
    {
        $this->initParameters = $initParameters;

        return $this;
    }

    /**
     * Set a single parameter
     *
     * setParameter('amount', 5000)
     * will call the method setAmount(5000)
     *
     * @param  $key
     * @param  $value
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->initParameters[$key] = $value;

        return $this;
    }

    /**
     * @param bool $doCompletePayment
     * @return PaymentFinishedResponse|RedirectResponse
     */
    public function pay($doCompletePayment = true, $testMode = true, $notificationMode = false)
    {
        $requiredParameters = array('amount', 'currency', 'description');
        foreach($requiredParameters as $requiredParameter){
            if(!array_key_exists($requiredParameter, $this->initParameters)){
                return new PaymentFinishedResponse(
                    PaymentFinishedResponse::STATUS_ERROR,
                    sprintf('The payment parameter "%s" must be set.', $requiredParameter)
                );
            }
        }

        /* @var Saferpay $saferpay */
        $saferpay = $this->getContainer()->get('payment.saferpay.handler');
        $payInitParameter = $this->getPayInitParameter($testMode);

        $request = $this->getContainer()->get('request');

        if ($notificationMode) {
            $saferpayData      = $request->request->get('DATA');
            $saferpaySignature = $request->request->get('SIGNATURE');
        } else {
            $saferpayData      = $request->query->get('DATA');
            $saferpaySignature = $request->query->get('SIGNATURE');
        }

        if($saferpayData){
            $responseDataXml = new \SimpleXMLElement(stripslashes($saferpayData));
        }

        switch($request->query->get('status')){
            case PaymentFinishedResponse::STATUS_OK:
                try {
                    $payConfirmParameter = $saferpay->verifyPayConfirm($saferpayData, $saferpaySignature);

                    if(true === $this->validatePayConfirmParameter($payConfirmParameter, $payInitParameter)){
                        if(false === $doCompletePayment){
                            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_COMPLETION, $responseDataXml->attributes());
                        }

                        $payCompleteResponse = $saferpay->payCompleteV2($payConfirmParameter, 'Settlement');
                        if($payCompleteResponse->getResult() == '0'){
                            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_OK, null, $responseDataXml->attributes());
                        }

                        return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_COMPLETION, $responseDataXml->attributes());
                    }

                    $saferpay->payCompleteV2($payConfirmParameter, 'Cancel');
                    return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_VALIDATION, $responseDataXml->attributes());
                }catch(\Exception $e){
                    return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, PaymentFinishedResponse::ERROR_VALIDATION, $responseDataXml->attributes());
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
            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, 'connectionerror', $responseDataXml->attributes());
        }catch(\Exception $e){
            return new PaymentFinishedResponse(PaymentFinishedResponse::STATUS_ERROR, 'connectionerror', $responseDataXml->attributes());
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

        $payInitParameter = $payInitFactory->createPayInitParameter();

        $providerSet = null;

        if(true === $testMode){
            $payInitParameter->setAccountid($payInitParameter::SAFERPAYTESTACCOUNT_ACCOUNTID);
            $payInitParameter->setPaymentmethods(array($payInitParameter::PAYMENTMETHOD_SAFERPAY_TESTCARD));
        }else{
            $payInitParameter->setPaymentmethods(array($payInitParameter::PAYMENTMETHOD_MASTERCARD, $payInitParameter::PAYMENTMETHOD_VISA));
        }

        foreach($this->initParameters as $key => $value){
            $methodName = 'set'.ucfirst($key);
            if(method_exists($payInitParameter, $methodName)){
                $payInitParameter->$methodName($value);
            }
        }

        return $payInitParameter;
    }

    /**
     * @return ContainerInterface
     */
    abstract protected function getContainer();
}