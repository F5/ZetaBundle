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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
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
    function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('zetaSearch.xml');

        $config = $config[0]["search"];

        foreach (array('solr', 'zendlucene', 'xml-manager') AS $comp) {
            if (isset($config[$comp])) {
                $container->setParameter($comp, $config[$comp]);
            }
        }

        if (isset($config['manager'])) {
            $container->setAlias('zeta.search.manager', $config['manager']);
        }
        if (isset($config['handler'])) {
            $container->setAlias('zeta.search.handler', $config['handler']);
        }
    }
}