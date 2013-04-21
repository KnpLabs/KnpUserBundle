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

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\UserBundle\DependencyInjection\Compiler\ValidationPass;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;

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

        if (! class_exists('Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass')) {
            // TODO: provide a temporary implementation of the compiler pass in FOSUserBundle
            // so people can already use the Model class even with older symfony?
            return;
        }

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'FOS\UserBundle\Model',
        );

        if (class_exists('Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass')) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_orm'));
        }

        if (class_exists('Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass')) {
            $container->addCompilerPass(DoctrineMongoDBMappingsPass::createXmlMappingDriver($mappings, array('fos_user.model_manager_name'), 'fos_user.backend_type_mongodb'));
        }

        // TODO: couch
    }
}
