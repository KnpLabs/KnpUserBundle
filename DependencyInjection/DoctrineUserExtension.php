<?php

namespace Bundle\DoctrineUserBundle\DependencyInjection;

use Symfony\Components\DependencyInjection\Extension\Extension;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Components\DependencyInjection\ContainerBuilder;

class DoctrineUserExtension extends Extension
{

    public function configLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('listener.xml');
        $loader->load('controller.xml');
        $loader->load('dao.xml');

        if(isset($config['user_object_class'])) {
            $container->setParameter('doctrine_user.user_object.class', $config['user_object_class']);
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 'auth';
    }
}
