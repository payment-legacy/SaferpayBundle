<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @var ContainerBuilder $container */

// parameters
$container->setParameter('payment.saferpay.key_value.class', 'Payment\Saferpay\SaferpayKeyValue');
$container->setParameter('payment.saferpay.config.class', 'Payment\Saferpay\SaferpayConfig');
$container->setParameter('payment.saferpay.class', 'Payment\Saferpay\Saferpay');
$container->setParameter('payment.saferpay.key_value.service.id', 'payment.saferpay.key_value');
$container->setParameter('payment.saferpay.config.service.id', 'payment.saferpay.config');

// services
$container
    ->setDefinition('payment.saferpay.key_value', new Definition('%payment.saferpay.key_value.class%'))
;
$container
    ->setDefinition('payment.saferpay.config.validators.init', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.validators.init%'))
;
$container
    ->setDefinition('payment.saferpay.config.validators.confirm', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.validators.confirm%'))
;
$container
    ->setDefinition('payment.saferpay.config.validators.complete', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.validators.complete%'))
;
$container
    ->setDefinition('payment.saferpay.config.defaults.init', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.defaults.init%'))
;
$container
    ->setDefinition('payment.saferpay.config.defaults.confirm', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.defaults.confirm%'))
;
$container
    ->setDefinition('payment.saferpay.config.defaults.complete', new Definition('%payment.saferpay.key_value.class%'))
    ->addMethodCall('all', array('%payment.saferpay.defaults.complete%'))
;
$container
    ->setDefinition('payment.saferpay.config', new Definition('%payment.saferpay.config.class%'))
    ->addMethodCall('setInitUrl', array('%payment.saferpay.urls.init%'))
    ->addMethodCall('setConfirmUrl', array('%payment.saferpay.urls.confirm%'))
    ->addMethodCall('setCompleteUrl', array('%payment.saferpay.urls.complete%'))
    ->addMethodCall('setInitValidationsConfig', array(new Reference('payment.saferpay.config.validators.init')))
    ->addMethodCall('setConfirmValidationsConfig', array(new Reference('payment.saferpay.config.validators.confirm')))
    ->addMethodCall('setCompleteValidationsConfig', array(new Reference('payment.saferpay.config.validators.complete')))
    ->addMethodCall('setInitDefaultsConfig', array(new Reference('payment.saferpay.config.defaults.init')))
    ->addMethodCall('setConfirmDefaultsConfig', array(new Reference('payment.saferpay.config.defaults.confirm')))
    ->addMethodCall('setCompleteDefaultsConfig', array(new Reference('payment.saferpay.config.defaults.complete')))
;
$container
    ->setDefinition('payment.saferpay', new Definition('%payment.saferpay.class%'))
    ->addMethodCall('setKeyValuePrototype', array(new Reference($container->getParameter('payment.saferpay.key_value.service.id'))))
    ->addMethodCall('setConfig', array(new Reference($container->getParameter('payment.saferpay.config.service.id'))))
;