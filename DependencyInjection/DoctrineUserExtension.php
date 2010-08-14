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
        $loader->load('form.xml');

        if(!isset($config['db_driver'])) {
            throw new \InvalidConfigurationException('You must provide the auth.db_driver configuration');
        }
        if('orm' === $config['db_driver']) {
            $loader->load('orm.xml');
        }
        elseif('odm' === $config['db_driver']) {
            $loader->load('odm.xml');
        }
        else {
            throw new \InvalidConfigurationException(sprintf('The %s driver is not supported', $config['db_driver']));
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
