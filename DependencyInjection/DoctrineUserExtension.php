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

        if (!isset($config['classes']['user'])) {
            throw new \InvalidArgumentException('You must define your user class');
        }

        $namespaces = array(
            '' => array(
                'session_create_success_route' => 'doctrine_user.session_create.success_route',
                'template_renderer' => 'doctrine_user.template.renderer',
            ),
            'classes' => 'doctrine_user.%s.class',
            'auth' => 'doctrine_user.auth.%s',
            'form_names' => 'doctrine_user.%s_form.name',
            'confirmation_email' => 'doctrine_user.confirmation_email.%s',
        );
        $this->remapParametersNamespaces($config, $container, $namespaces);
    }

    protected function remapParameters($config, $container, $map)
    {
        foreach ($map as $name => $paramName) {
            if (!isset($config[$name])) {
                continue;
            }
            $container->setParameter($paramName, $config[$name]);
        }
    }

    protected function remapParametersNamespaces($config, $container, $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!isset($config[$ns])) {
                    continue;
                }
                $config = $config[$ns];
            }
            if (is_array($map)) {
                $this->remapParameters($config, $container, $map);
            } else {
                foreach ($config as $name => $value) {
                    $container->setParameter(sprintf($map, $name), $value);
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
