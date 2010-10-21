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
            $loader->load(sprintf('%s.%s', $config['db_driver'], 'xml'));
        }
        catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('The db_driver "%s" is not supported by doctrine_user', $config['db_driver']));
        }

        if (isset($config['user_class'])) {
            $container->setParameter('doctrine_user.user_object.class', $config['user_class']);
        }
        else {
            throw new \InvalidArgumentException('You must define your user class');
        }

        if (isset($config['group_class'])) {
            $container->setParameter('doctrine_user.group_object.class', $config['group_class']);
        }

        if (isset($config['permission_class'])) {
            $container->setParameter('doctrine_user.permission_object.class', $config['permission_class']);
        }

        if (isset($config['session_create_success_route'])) {
            $container->setParameter('doctrine_user.session_create.success_route', $config['session_create_success_route']);
        }

        if (isset($config['template_renderer'])) {
            $container->setParameter('doctrine_user.template.renderer', $config['template_renderer']);
        }

        if (isset($config['confirmation_email']) && is_array($config['confirmation_email'])) {
            $confirmationEmailConfig = $config['confirmation_email'];

            if (isset($confirmationEmailConfig['enabled'])) {
                $container->setParameter('doctrine_user.confirmation_email.enabled', $confirmationEmailConfig['enabled']);
            }

            if (isset($confirmationEmailConfig['from_email'])) {
                $container->setParameter('doctrine_user.confirmation_email.from_email', $confirmationEmailConfig['from_email']);
            }

            if (isset($confirmationEmailConfig['template'])) {
                $container->setParameter('doctrine_user.confirmation_email.template', $confirmationEmailConfig['template']);
            }
        }

        if (isset($config['auth_class'])) {
            $container->setParameter('doctrine_user.auth.class', $config['auth_class']);
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
