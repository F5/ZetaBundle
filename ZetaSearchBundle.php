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

namespace F5\Bundle\ZetaSearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use F5\Bundle\ZetaSearchBundle\DependencyInjection\ZetaSearchExtension;
use Symfony\Component\DependencyInjection\Loader\Loader;

class ZetaSearchBundle extends Bundle
{
    /**
     * Customizes the Container instance.
     *
     * @param Symfony\Component\DependencyInjection\ContainerInterface $container A ContainerInterface instance
     *
     * @return Symfony\Component\DependencyInjection\BuilderConfiguration A BuilderConfiguration instance
     */
    public function buildContainer(ContainerInterface $container)
    {
        Loader::registerExtension(new ZetaSearchExtension());
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}