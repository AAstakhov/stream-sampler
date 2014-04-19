<?php

namespace ResearchGate\StreamSampling\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root( 'config' );

        $rootNode
            ->children()
                ->arrayNode( 'inputs' )
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->prototype('array')
                    ->children()
                        ->scalarNode('kind')->isRequired()->end()
                        ->scalarNode('description')->isRequired()->end()
                        ->booleanNode('use_utf8')->isRequired()->end()
                        ->arrayNode('parameters')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')
                        ->end()

                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}