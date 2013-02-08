<?php

namespace Payment\Bundle\SaferpayBundle\DependencyInjection;

use Payment\Saferpay\Saferpay;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

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
        $this->saferpayConfig = Saferpay::getSaferpayConfig();

        $treeBuilder = new TreeBuilder();
        $root = $this->getRoot($treeBuilder);

        $this
            ->addUrlSection($root)
            ->addValidatorsSection($root)
            ->addDefaultsSection($root)
        ;

        return $treeBuilder;
    }

    /**
     * @param TreeBuilder $treeBuilder
     * @return NodeBuilder
     */
    protected function getRoot(TreeBuilder $treeBuilder)
    {
        return $treeBuilder->root('payment_saferpay')->children();
    }

    /**
     * @param NodeBuilder $root
     * @throws InvalidArgumentException
     * @return Configuration
     */
    protected function addUrlSection(NodeBuilder $root)
    {
        if(!isset($this->saferpayConfig['urls'])){
            throw new InvalidArgumentException("SaferpayConfig invalid - urls section not given");
        }

        $children = $root->arrayNode('urls')->addDefaultsIfNotSet()->children();
        foreach($this->saferpayConfig['urls'] as $key => $value){
            $children->scalarNode($key)->defaultValue($value)->cannotBeEmpty();
        }

        return $this;
    }

    /**
     * @param NodeBuilder $root
     * @throws InvalidArgumentException
     * @return Configuration
     */
    protected function addValidatorsSection(NodeBuilder $root)
    {
        if(!isset($this->saferpayConfig['validators'])){
            throw new InvalidArgumentException("SaferpayConfig invalid - validators section not given");
        }

        $children = $root->arrayNode('validators')->addDefaultsIfNotSet()->children();
        foreach($this->saferpayConfig['validators'] as $key => $validator){
            $validatorChildren = $children->arrayNode($key)->addDefaultsIfNotSet()->children();
            foreach($validator as $key => $value){
                $validatorChildren->scalarNode($key)->defaultValue($value)->cannotBeEmpty();
            }
        }

        return $this;
    }

    /**
     * @param NodeBuilder $root
     * @throws InvalidArgumentException
     * @return Configuration
     */
    protected function addDefaultsSection(NodeBuilder $root)
    {
        if(!isset($this->saferpayConfig['defaults'])){
            throw new InvalidArgumentException("SaferpayConfig invalid - defaults section not given");
        }

        $children = $root->arrayNode('defaults')->addDefaultsIfNotSet()->children();
        foreach($this->saferpayConfig['defaults'] as $key => $validator){
            $defaultsChildren = $children->arrayNode($key)->addDefaultsIfNotSet()->children();
            foreach($validator as $key => $value){
                $defaultsChildren->scalarNode($key)->defaultValue($value)->cannotBeEmpty();
            }
        }

        return $this;
    }
}