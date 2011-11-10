<?php

namespace F5\Bundle\ZetaSearchBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('zeta_search');

        $rootNode
            ->children()
                ->scalarNode("handler")->defaultValue("zeta_search.handler.solr")->end()
                ->scalarNode("manager")->defaultValue("zeta_search.manager.embedded")->end()
                ->arrayNode("xml-manager")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("path")->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode("solr")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("host")->defaultValue("localhost")->end()
                        ->scalarNode("port")->defaultValue(8983)->end()
                        ->scalarNode("location")->defaultValue("/solr")->end()
                    ->end()
                ->end()
                ->arrayNode("zendlucene")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("data_dir")->defaultValue("/tmp/lucene")->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
