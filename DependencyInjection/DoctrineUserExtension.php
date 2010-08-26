<?php

namespace Bundle\DoctrineUserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoctrineUserExtension extends Extension
{

    public function configLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('auth.xml');
        $loader->load('form.xml');
        $loader->load('controller.xml');
        $loader->load('templating.xml');

        if(!isset($config['db_driver'])) {
            throw new \InvalidArgumentException('You must provide the doctrine_user.db_driver configuration');
        }

        try {
            $loader->load(sprintf('%s.%s', $config['db_driver'], 'xml'));
        }
        catch(\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('The db_driver "%s" is not supported by doctrine_user', $config['db_driver']));
        }

        if(isset($config['user_class'])) {
            $container->setParameter('doctrine_user.user_object.class', $config['user_class']);
        }
        
        if(isset($config['session_create_success_route'])) {
            $container->setParameter('doctrine_user.session_create.success_route', $config['session_create_success_route']);
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/doctrine_user';
    }

    public function getAlias()
    {
        return 'doctrine_user';
    }
}
