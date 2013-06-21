<?php

namespace Payment\Bundle\SaferpayBundle\PayInitParameter;

use Payment\Saferpay\Data\PayInitParameterWithDataInterface;

class PayInitParameterFactory
{
    /**
     * @var PayInitParameterWithDataInterface
     */
    protected $payInitParameter;

    public function __construct(PayInitParameterWithDataInterface $payInitParameter, array $payInitParameterData)
    {
        foreach($payInitParameterData as $key => $value){
            if(!is_null($value)){
                $method = 'set'.ucfirst($key);
                $payInitParameterData->{$method}($value);
            }
        }
    }

    /**
     * @return PayInitParameterWithDataInterface
     */
    public function getPayInitParameter()
    {
        return clone $this->payInitParameter;
    }
}