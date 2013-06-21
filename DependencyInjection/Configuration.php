<?php

namespace Payment\Bundle\SaferpayBundle\DependencyInjection;

use Payment\Saferpay\Saferpay;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $saferpayConfig;

    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('payment_saferpay');

        $rootNode
            ->children()
                ->arrayNode('logger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('serviceid')->defaultValue('logger')->end()
                    ->end()
                ->end()
                ->arrayNode('httpclient')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('serviceid')->defaultValue('payment.saferpay.httpclient.buzz')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}