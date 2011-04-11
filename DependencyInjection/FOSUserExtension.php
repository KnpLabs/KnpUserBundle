<?php

namespace FOS\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FOSUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array(strtolower($config['db_driver']), array('orm', 'mongodb'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        foreach (array('controller', 'form', 'validator', 'security', 'util', 'listener') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        if (!empty($config['service']['util']['mailer'])) {
            $container->setAlias('fos_user.util.mailer', $config['service']['util']['mailer']);
        }

        if (!empty($config['group'])) {
            $loader->load('group.xml');
            $loader->load(sprintf('%s_group.xml', $config['db_driver']));
            $this->remapParametersNamespaces($config['group'], $container, array(
                'class' => 'fos_user.%s.group.class',
                '' => array(
                    'form_name' => 'fos_user.form.group.name',
                    'form_validation_groups' => 'fos_user.form.group.validation_groups'
                ),
            ));
        }

        $this->remapParametersNamespaces($config, $container, array(
            ''          => array(
                'firewall_name' => 'fos_user.firewall_name'
            ),
            'encoder'   => 'fos_user.encoder.%s',
            'template'  => 'fos_user.template.%s',
            'form_name' => 'fos_user.form.%s.name',
            'form_validation_groups' => 'fos_user.form.%s.validation_groups',
        ));

        $this->remapParametersNamespaces($config['class'], $container, array(
            'model'      => 'fos_user.model.%s.class',
            'form'       => 'fos_user.form.%s.class',
            'controller' => 'fos_user.controller.%s.class',
            'util'       => 'fos_user.util.%s.class',
        ));

        $this->remapParametersNamespaces($config['email'], $container, array(
            ''                   => array('from_email' => 'fos_user.email.from_email'),
            'confirmation'       => 'fos_user.email.confirmation.%s',
            'resetting_password' => 'fos_user.email.resetting_password.%s',
        ));
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
}
