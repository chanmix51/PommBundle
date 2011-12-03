<?php

namespace GHub\PommBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('g_hub_pomm');

        $rootNode
            ->beforeNormalization()
                ->ifTrue(function($v) {
                    return isset($v['converters']) && is_array($v['converters']);
                })
                ->then(function($v) {
                    foreach ($v['databases'] as &$database) {
                        $database['converters'] = array_merge($v['converters'], @(array)$database['converters']);
                    }
                    unset($v['converters']);

                    return $v;
                })
            ->end()
            ->children()
                ->arrayNode('databases')
                    ->useAttributeAsKey('key')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('dsn')->isRequired()->end()
                            ->scalarNode('class')->defaultValue('Pomm\Connection\Database')->end()
                            ->scalarNode('isolation')->defaultNull()->end()
                            ->arrayNode('converters')
                                ->useAttributeAsKey('key')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('class')->isRequired()->end()
                                        ->arrayNode('types')->isRequired()->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

