<?php
/*
 * ZetaComponents Search Bundle
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so I can send you a copy immediately.
 */

namespace F5\Bundle\ZetaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ZetaExtension extends Extension
{
    public function getAlias()
    {
        return 'zeta';
    }

    public function getNamespace()
    {
        return 'http://www.whitewashing.de/schema/zeta/';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->remapParametersNamespaces($config["search"], $container, array(
            "solr" => "zeta.search.solr.%s",
        ));

        $this->remapParametersNamespaces($config["search"], $container, array(
            "xml-manager" => "zeta.search.xml-manager.%s",
        ));

        $this->remapParametersNamespaces($config["search"], $container, array(
            "zendlucene" => "zeta.search.zendlucene.%s",
        ));

        if(null !== $config["search"]) {
            $container->setAlias('zeta.search.manager', $config['search']['manager']);
            $container->setAlias('zeta.search.handler', $config['search']['handler']);
        }
        
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!array_key_exists($ns, $config)) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
                }
            }
        }
    }
}