<?php

namespace Mcfedr\ResqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('mcfedr_resque')
            ->children()
                ->booleanNode('debug')->defaultFalse()->end()
                ->scalarNode('host')->defaultValue('localhost')->cannotBeEmpty()->end()
                ->integerNode('port')->min(0)->max(65535)->defaultValue(6379)->end()
                ->scalarNode('default_queue')->defaultValue('default')->cannotBeEmpty()->end()
                ->scalarNode('prefix')->end()
                ->booleanNode('track_status')->defaultFalse()->end()
            ->end()
        ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
