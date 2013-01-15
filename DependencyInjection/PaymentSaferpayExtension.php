<?php

namespace Payment\Bundle\SaferpayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class PaymentSaferpayExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // load the configuration
        $configuration = new Configuration();

        // load the configuration processor
        $processor = new Processor();

        // process the config
        $config = $processor->process($configuration->getTree(), $configs);

        // add parameter
        $container->setParameter('payment.saferpay.urls.init', $config['urls']['init']);
        $container->setParameter('payment.saferpay.urls.confirm', $config['urls']['confirm']);
        $container->setParameter('payment.saferpay.urls.complete', $config['urls']['complete']);

        $container->setParameter('payment.saferpay.validators.init', $config['validators']['init']);
        $container->setParameter('payment.saferpay.validators.confirm', $config['validators']['confirm']);
        $container->setParameter('payment.saferpay.validators.complete', $config['validators']['complete']);

        $container->setParameter('payment.saferpay.defaults.init', $config['defaults']['init']);
        $container->setParameter('payment.saferpay.defaults.confirm', $config['defaults']['confirm']);
        $container->setParameter('payment.saferpay.defaults.complete', $config['defaults']['complete']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.php');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'payment_saferpay';
    }
}