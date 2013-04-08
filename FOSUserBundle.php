<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle;

use FOS\UserBundle\DependencyInjection\Compiler\ValidationPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\CompilerPass\RegisterMappingsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Matthieu Bontemps <matthieu@knplabs.com>
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class FOSUserBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ValidationPass());

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\CompilerPass\RegisterMappingsPass')) {
            $mappings = array(
                realpath(__DIR__.'/Resources/config/doctrine/model') => 'FOS\UserBundle\Model',
            );
            $container->addCompilerPass(new RegisterMappingsPass($mappings, 'xml', 'fos_user.backend_type_orm'));
        }

        // TODO: couch, mongo
    }
}
