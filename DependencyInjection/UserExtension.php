<?php

namespace FOS\UserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class UserExtension extends Extension
{
    public function configLoad(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->doConfigLoad($config, $container);
        }
    }

    public function doConfigLoad(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');

        // ensure the db_driver is configured
        if (!isset($config['db_driver']) && !$container->hasDefinition('fos_user.user_manager')) {
            throw new \InvalidArgumentException('The db_driver parameter must be defined.');
        }
        if (isset($config['db_driver'])){
            if (!in_array(strtolower($config['db_driver']), array('orm', 'mongodb'))) {
                throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
            }
            if ($container->hasDefinition('fos_user.user_manager')){
                throw new \InvalidArgumentException('The db_driver parameter cannot be defined twice.');
            }
            $loader->load(sprintf('%s.xml', $config['db_driver']));
        }

        // load all service configuration files (the db_driver first)
        if (!$container->hasDefinition('security.encoder.fos_user')) {
            foreach (array('controller', 'templating', 'email', 'twig', 'form', 'validator', 'security', 'util') as $basename) {
                $loader->load(sprintf('%s.xml', $basename));
            }
        }

        // ensure the user model class is configured
        if (!isset($config['class']['model']['user']) && !$container->hasParameter('fos_user.model.user.class')) {
            throw new \InvalidArgumentException('The user model class must be defined');
        }

        // per default, we use a sha512 encoder, but you may change this here
        if (isset($config['encoder'])) {
            $this->remapParameters($config['encoder'], $container, array(
                'algorithm'        => 'fos_user.encoder.algorithm',
                'encode_as_base64' => 'fos_user.encoder.encode_as_base64',
                'iterations'       => 'fos_user.encoder.iterations',
            ));
        }

        $this->remapParametersNamespaces($config, $container, array(
            ''          => array('session_create_success_route' => 'fos_user.session_create.success_route'),
            'template'  => 'fos_user.template.%s',
            'form_name' => 'fos_user.form.%s.name',
        ));

        if (isset($config['class'])){
            $this->remapParametersNamespaces($config['class'], $container, array(
                'model'      => 'fos_user.model.%s.class',
                'form'       => 'fos_user.form.%s.class',
                'controller' => 'fos_user.controller.%s.class',
                'util'       => 'fos_user.util.%s.class',
            ));
        }

        if (isset($config['email'])){
            $this->remapParametersNamespaces($config['email'], $container, array(
                ''                   => array('from_email' => 'fos_user.email.from_email'),
                'confirmation'       => 'fos_user.email.confirmation.%s',
                'resetting_password' => 'fos_user.email.resetting_password.%s',
            ));
        }
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
                    if (null !== $value) {
                        $container->setParameter(sprintf($map, $name), $value);
                    }
                }
            }
        }
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getXsdValidationBasePath()
     *
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getNamespace()
     *
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/fos_user';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getAlias()
     *
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'fos_user';
    }
}
