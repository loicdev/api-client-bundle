<?php

namespace Geny\ApiClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('geny_api_client');

        $rootNode->children()
                    ->arrayNode('api')
                        ->useAttributeAsKey('')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('endpoint_root')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('security_token')->defaultNull()->end()
                                ->scalarNode('log')->defaultValue(false)->end()
                                ->scalarNode('profiler')->defaultValue(false)->end()
                                ->scalarNode('client')->defaultNull()->end()
                                ->arrayNode('cache')
                                        ->children()
                                            ->scalarNode('service')->defaultNull()->end()
                                            ->scalarNode('ttl')->defaultValue(false)->end()
                                            ->scalarNode('use_header_ttl')->defaultValue(false)->end()
                                            ->scalarNode('cache_server_errors')->defaultValue(false)->end()
                                            ->scalarNode('cache_client_errors')->defaultValue(false)->end()
                                        ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
