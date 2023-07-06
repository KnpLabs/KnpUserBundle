<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\DependencyInjection\Compiler;

use FOS\UserBundle\Util\PasswordUpdater;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
final class ConfigurePasswordHasherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('security.password_hasher_factory')) {
            return;
        }

        // If we don't have the new service for password-hasher, use the old implementation based on the EncoderFactoryInterface
        $def = $container->getDefinition('fos_user.util.password_updater');

        $def->setClass(PasswordUpdater::class);
        $def->setArgument(0, new Reference('security.encoder_factory'));
    }
}
