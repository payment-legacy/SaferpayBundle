<?php

namespace Payment\Bundle\SaferpayBundle\PayInitParameter;

use Payment\Saferpay\Data\PayInitParameterWithDataInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class PayInitParameterFactory
{
    /**
     * @var PayInitParameterWithDataInterface
     */
    protected $payInitParameter;

    /**
     * @param PayInitParameterWithDataInterface $payInitParameter
     * @param array $payInitParameterData
     */
    public function __construct(PayInitParameterWithDataInterface $payInitParameter, array $payInitParameterData = array())
    {
        foreach($payInitParameterData as $key => $value){
            if(is_null($value)){
                continue;
            }
            $method = 'set'.ucfirst($key);
            if(!method_exists($payInitParameter, $method)){
                throw new InvalidArgumentException($method .' Method not found');
            }
            $payInitParameter->{$method}($value);
        }
        $this->payInitParameter = $payInitParameter;
    }

    /**
     * @return PayInitParameterWithDataInterface
     */
    public function createPayInitParameter()
    {
        return clone $this->payInitParameter;
    }
}