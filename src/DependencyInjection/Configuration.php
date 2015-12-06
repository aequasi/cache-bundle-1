<?php

/*
 * This file is part of php-cache\cache-bundle package.
 *
 * (c) 2015-2015 Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\CacheBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('cache');

        $rootNode->children()
            ->append($this->addSessionSupportSection())
            ->append($this->addDoctrineSection())
            ->append($this->addRouterSection())
            ->end();

        return $treeBuilder;
    }

    /**
     * Configure the "aequasi_cache.session" section
     *
     * @return ArrayNodeDefinition
     */
    private function addSessionSupportSection()
    {
        $tree = new TreeBuilder();
        $node = $tree->root('session');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
                ->scalarNode('instance')->end()
                ->scalarNode('prefix')
                    ->defaultValue("session_")
                ->end()
                ->scalarNode('ttl')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Configure the "aequasi_cache.doctrine" section
     *
     * @return ArrayNodeDefinition
     */
    private function addDoctrineSection()
    {
        $tree = new TreeBuilder();
        $node = $tree->root('doctrine');

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                    ->isRequired()
                ->end()
            ->end()
        ;

        $types = array('metadata', 'result', 'query');
        foreach ($types as $type) {
            $node->children()
                    ->arrayNode($type)
                        ->canBeUnset()
                        ->children()
                            ->scalarNode('instance')->end()
                            ->arrayNode('entity_managers')
                                ->defaultValue(array())
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(
                                        function ($v) {
                                            return (array) $v;
                                        }
                                    )
                                    ->end()
                                    ->prototype('scalar')->end()
                                ->end()
                            ->arrayNode('document_managers')
                                ->defaultValue(array())
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(
                                        function ($v) {
                                            return (array) $v;
                                        }
                                    )
                                ->end()
                                ->prototype('scalar')->end()
                            ->end()
                    ->end()
                ->end();
        }

        return $node;
    }

    /**
     * Configure the "aequasi_cache.router" section
     *
     * @return ArrayNodeDefinition
     */
    private function addRouterSection()
    {
        $tree = new TreeBuilder();
        $node = $tree->root('router');

        $node->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
                ->scalarNode('instance')
                    ->defaultNull()
                ->end()
            ->end();

        return $node;
    }
}
