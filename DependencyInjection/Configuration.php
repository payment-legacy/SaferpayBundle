<?php

namespace Payment\Bundle\SaferpayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var array
     */
    protected $saferpayConfig;

    /**
     * @var string
     */
    protected $payInitParameterInterface = "Payment\\Saferpay\\Data\\PayInitParameterInterface";

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

        $this->addPayInitSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param NodeDefinition $node
     */
    protected function addPayInitSection(NodeDefinition $node)
    {
        $payInitSection = $node->children()->arrayNode('payinitparameter')->addDefaultsIfNotSet()->children();

        $payInitSection->scalarNode('serviceid')->defaultValue('payment.saferpay.payinitparameter.default');

        $payInitSectionData = $payInitSection->arrayNode('data')->addDefaultsIfNotSet()->children();

        $payInitReflection = new \ReflectionClass($this->payInitParameterInterface);
        foreach($payInitReflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method){
            if(substr($method->getName(), 0, 3) != 'set'){
                continue;
            }
            $payInitSectionData->scalarNode(lcfirst(substr($method->getName(), 3)))->defaultValue(null);
        }
    }
}