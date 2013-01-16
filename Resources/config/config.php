<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Container;

/** @var Container $container */

$container->setParameter('payment.saferpay.key_value.class', 'Payment\Saferpay\SaferpayKeyValue');
$container->setParameter('payment.saferpay.config.class', 'Payment\Saferpay\SaferpayConfig');
$container->setParameter('payment.saferpay.class', 'Payment\Saferpay\Saferpay');

$container
    ->setDefinition('payment.saferpay.config', new Definition('%payment.saferpay.config.class%'))
    ->addMethodCall('setInitUrl', array('%payment.saferpay.urls.init%'))
    ->addMethodCall('setConfirmUrl', array('%payment.saferpay.urls.confirm%'))
    ->addMethodCall('setCompleteUrl', array('%payment.saferpay.urls.complete%'))
    ->addMethodCall('setInitValidationsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.validators.init%'))))
    ->addMethodCall('setConfirmValidationsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.validators.confirm%'))))
    ->addMethodCall('setCompleteValidationsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.validators.complete%'))))
    ->addMethodCall('setInitDefaultsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.defaults.init%'))))
    ->addMethodCall('setConfirmDefaultsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.defaults.confirm%'))))
    ->addMethodCall('setCompleteDefaultsConfig', array(new Definition('%payment.saferpay.key_value.class%', array('%payment.saferpay.defaults.complete%'))))
;

$container
    ->setDefinition('payment.saferpay', new Definition('%payment.saferpay.class%'))
    ->addMethodCall('setConfig', array(new Reference('payment.saferpay.config')))
;