<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container
    ->setDefinition('payment_saferpay_config', new Definition('Payment\Saferpay\SaferpayConfig'))
    ->addMethodCall('setInitUrl', array('%payment.saferpay.urls.init%'))
    ->addMethodCall('setConfirmUrl', array('%payment.saferpay.urls.confirm%'))
    ->addMethodCall('setCompleteUrl', array('%payment.saferpay.urls.complete%'))
    ->addMethodCall('setInitValidationsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.validators.init%'))))
    ->addMethodCall('setConfirmValidationsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.validators.confirm%'))))
    ->addMethodCall('setCompleteValidationsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.validators.complete%'))))
    ->addMethodCall('setInitDefaultsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.defaults.init%'))))
    ->addMethodCall('setConfirmDefaultsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.defaults.confirm%'))))
    ->addMethodCall('setCompleteDefaultsConfig', array(new Definition('Payment\Saferpay\SaferpayKeyValue', array('%payment.saferpay.defaults.complete%'))))
;

$container
    ->setDefinition('payment_saferpay', new Definition('Payment\Saferpay\Saferpay'))
    ->addMethodCall('setConfig', array(new Reference('payment_saferpay_config')))
    ->addMethodCall('setHttpClient', array(new Definition('Payment\Saferpay\Http\Client\GuzzleClient')))
    //->addMethodCall('setLogger', array(new Reference('logger')))
;