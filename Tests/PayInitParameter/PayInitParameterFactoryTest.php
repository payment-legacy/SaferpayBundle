<?php

namespace Payment\Bundle\SaferpayBundle\Tests\PayInitParameter;

use Payment\Saferpay\Data\PayInitParameter;
use Payment\Bundle\SaferpayBundle\PayInitParameter\PayInitParameterFactory;

class PayInitParameterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatePayInitParameter()
    {
        $factory = new PayInitParameterFactory(new PayInitParameter(), array(
            'accountid' => '99867-94913159',
            'amount' => 50050,
            'currency' => 'CHF'
        ));

        $payInitParameter = $factory->createPayInitParameter();

        $this->assertEquals('99867-94913159', $payInitParameter->getAccountid());
        $this->assertEquals(50050, $payInitParameter->getAmount());
        $this->assertEquals('CHF', $payInitParameter->getCurrency());
    }
}