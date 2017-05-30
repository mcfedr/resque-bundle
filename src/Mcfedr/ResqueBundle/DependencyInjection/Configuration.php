<?php

namespace Mcfedr\ResqueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('mcfedr_resque')
            ->children()
                ->booleanNode('debug')->defaultFalse()->end()
                ->scalarNode('host')->defaultValue('localhost')->cannotBeEmpty()->end()
                ->scalarNode('port')->defaultValue(6379)->end()
                ->scalarNode('default_queue')->defaultValue('default')->cannotBeEmpty()->end()
                ->scalarNode('prefix')->end()
                ->booleanNode('track_status')->defaultFalse()->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
