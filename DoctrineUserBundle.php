<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle;

use Bundle\DoctrineUserBundle\DependencyInjection\DoctrineUserExtension;
use Symfony\Framework\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\DependencyInjection\Loader\Loader;

class DoctrineUserBundle extends BaseBundle
{
    public function buildContainer(ContainerInterface $container)
    {
        Loader::registerExtension(new DoctrineUserExtension());
    }

    public function boot(ContainerInterface $container)
    {
        $container->getDoctrineUserAuthListenerService()->connect();
    }
}
