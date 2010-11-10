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
        $loader->load('email.xml');

        if (!isset($config['db_driver'])) {
            throw new \InvalidArgumentException('You must provide the doctrine_user.db_driver configuration');
        }

        try {
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('The db_driver "%s" is not supported by doctrine_user', $config['db_driver']));
        }

        if (!isset($config['class']['model']['user'])) {
            throw new \InvalidArgumentException('You must define your user model class');
        }

        $namespaces = array(
            '' => array(
                'session_create_success_route' => 'doctrine_user.session_create.success_route',
            ),
            'template' => 'doctrine_user.template.%s',
            'auth' => 'doctrine_user.auth.%s',
            'remember_me' => 'doctrine_user.remember_me.%s',
            'form_name' => 'doctrine_user.form.%s.name',
            'confirmation_email' => 'doctrine_user.confirmation_email.%s',
        );
        $this->remapParametersNamespaces($config, $container, $namespaces);

        $namespaces = array(
            'model' => 'doctrine_user.model.%s.class',
            'form' => 'doctrine_user.form.%s.class',
            'controller' => 'doctrine_user.controller.%s.class'
        );
        $this->remapParametersNamespaces($config['class'], $container, $namespaces);
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!isset($config[$ns])) {
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
                    if(null !== $value) {
                        $container->setParameter(sprintf($map, $name), $value);
                    }
                }
            }
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
