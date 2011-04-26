<?php

namespace FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use FOS\UserBundle\DependencyInjection\FOSUserExtension;
use Symfony\Component\Yaml\Parser;

class FOSUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['db_driver']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverIsValid()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $config['db_driver'] = 'foo';
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessFirewallNameSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['firewall_name']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessGroupModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        unset($config['group']['class']['model']);
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessUserModelClassSet()
    {
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        unset($config['class']['model']['user']);
        $loader->load(array($config), new ContainerBuilder());
    }

    public function testUserLoadModelClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Acme\MyBundle\Document\User', 'fos_user.model.user.class');
    }

    public function testUserLoadModelClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Entity\User', 'fos_user.model.user.class');
    }

    public function testUserLoadManagerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
    }

    public function testUserLoadManagerClass()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.user_manager');
    }

    public function testUserLoadFormClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Form\UserForm', 'fos_user.form.user.class');
        $this->assertParameter('FOS\UserBundle\Form\ChangePasswordForm', 'fos_user.form.change_password.class');
        $this->assertParameter('FOS\UserBundle\Form\ResetPasswordForm', 'fos_user.form.reset_password.class');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Form\User', 'fos_user.form.user.class');
        $this->assertParameter('Acme\MyBundle\Form\Group', 'fos_user.form.group.class');
        $this->assertParameter('Acme\MyBundle\Form\ChangePassword', 'fos_user.form.change_password.class');
        $this->assertParameter('Acme\MyBundle\Form\ResetPassword', 'fos_user.form.reset_password.class');
    }

    public function testUserLoadFormNameWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_user_form', 'fos_user.form.user.name');
        $this->assertParameter('fos_user_change_password_form', 'fos_user.form.change_password.name');
        $this->assertParameter('fos_user_reset_password_form', 'fos_user.form.reset_password.name');
    }

    public function testUserLoadFormName()
    {
        $this->createFullConfiguration();

        $this->assertParameter('acme_user_form', 'fos_user.form.user.name');
        $this->assertParameter('acme_group_form', 'fos_user.form.group.name');
        $this->assertParameter('acme_change_form', 'fos_user.form.change_password.name');
        $this->assertParameter('acme_reset_form', 'fos_user.form.reset_password.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.change_password');
        $this->assertHasDefinition('fos_user.form.reset_password');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.group');
        $this->assertHasDefinition('fos_user.form.change_password');
        $this->assertHasDefinition('fos_user.form.reset_password');
    }

    public function testUserLoadControllerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('FOS\UserBundle\Controller\UserController', 'fos_user.controller.user.class');
        $this->assertParameter('FOS\UserBundle\Controller\SecurityController', 'fos_user.controller.security.class');
    }

    public function testUserLoadControllerClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('Acme\MyBundle\Controller\UserController', 'fos_user.controller.user.class');
        $this->assertParameter('Acme\MyBundle\Controller\GroupController', 'fos_user.controller.group.class');
        $this->assertParameter('Acme\MyBundle\Controller\SecurityController', 'fos_user.controller.security.class');
    }

    public function testUserLoadControllerServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.controller.user');
        $this->assertHasDefinition('fos_user.controller.security');
    }

    public function testUserLoadControllerService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.controller.user');
        $this->assertHasDefinition('fos_user.controller.group');
        $this->assertHasDefinition('fos_user.controller.security');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.email.confirmation.enabled');
        $this->assertParameter(array('webmaster@example.com' => 'webmaster'), 'fos_user.email.from_email');
        $this->assertParameter('FOSUserBundle:User:confirmationEmail', 'fos_user.email.confirmation.template');
        $this->assertParameter('FOSUserBundle:User:resettingPasswordEmail', 'fos_user.email.resetting_password.template');
        $this->assertParameter(86400, 'fos_user.email.resetting_password.token_ttl');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter(true, 'fos_user.email.confirmation.enabled');
        $this->assertParameter(array('admin@acme.org' => 'Acme Corp'), 'fos_user.email.from_email');
        $this->assertParameter('AcmeMyBundle:Mail:confirmation', 'fos_user.email.confirmation.template');
        $this->assertParameter('AcmeMyBundle:Mail:resetting', 'fos_user.email.resetting_password.template');
        $this->assertParameter(1800, 'fos_user.email.resetting_password.token_ttl');
    }

    public function testUserLoadTemplateConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('twig', 'fos_user.template.engine');
        $this->assertParameter('TwigBundle::form.html.twig', 'fos_user.template.theme');
    }

    public function testUserLoadTemplateConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('php', 'fos_user.template.engine');
        $this->assertParameter('AcmeMyBundle:Form:theme.html.twig', 'fos_user.template.theme');
    }

    public function testUserLoadEncoderConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('sha512', 'fos_user.encoder.algorithm');
        $this->assertParameter(false, 'fos_user.encoder.encode_as_base64');
        $this->assertParameter(1, 'fos_user.encoder.iterations');
    }

    public function testUserLoadEncoderConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('sha1', 'fos_user.encoder.algorithm');
        $this->assertParameter(true, 'fos_user.encoder.encode_as_base64');
        $this->assertParameter(3, 'fos_user.encoder.iterations');
    }

    public function testUserLoadUtilServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertAlias('fos_user.mailer.real', 'fos_user.mailer');
    }

    public function testUserLoadUtilService()
    {
        $this->createFullConfiguration();

        $this->assertAlias('acme_my.mailer', 'fos_user.mailer');
    }

    /**
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new FOSUserExtension();
        $config = $this->getFullConfig();
        $loader->load(array($config), $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $yaml = <<<EOF
db_driver: mongodb
firewall_name: fos_user
class:
    model:
        user:  Acme\MyBundle\Document\User
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
db_driver: orm
firewall_name: fos_user
class:
    model:
        user: Acme\MyBundle\Entity\User
    form:
        user:            Acme\MyBundle\Form\User
        change_password: Acme\MyBundle\Form\ChangePassword
        reset_password:  Acme\MyBundle\Form\ResetPassword
    controller:
        user:     Acme\MyBundle\Controller\UserController
        security: Acme\MyBundle\Controller\SecurityController
service:
    util:
        mailer: acme_my.mailer
        email_canonicalizer:    acme_my.email_canonicalizer
        username_canonicalizer: acme_my.username_canonicalizer
encoder:
    algorithm:        sha1
    encode_as_base64: true
    iterations:       3
form_name:
    user:            acme_user_form
    change_password: acme_change_form
    reset_password:  acme_reset_form
form_validation_groups:
    user:            [test]
    change_password: [acme]
    reset_password:  [acme]
email:
    from_email: { admin@acme.org: Acme Corp }
    confirmation:
        enabled:    true
        template:   AcmeMyBundle:Mail:confirmation
    resetting_password:
        template:   AcmeMyBundle:Mail:resetting
        token_ttl:  1800
template:
    engine: php
    theme:  AcmeMyBundle:Form:theme.html.twig
group:
    class:
        model:      Acme\MyBundle\Entity\Group
        form:       Acme\MyBundle\Form\Group
        controller: Acme\MyBundle\Controller\GroupController
    form_name:              acme_group_form
    form_validation_groups: [acme]
EOF;
        $parser = new Parser();

        return  $parser->parse($yaml);
    }

    public function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->configuration->getAlias($key), sprintf('%s alias is correct', $key));
    }

    public function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    public function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ?: $this->configuration->hasAlias($id)));
    }

    protected function tearDown()
    {
        unset($this->configuration);
    }

}
