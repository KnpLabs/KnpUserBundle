<?php

namespace Bundle\DoctrineUserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bundle\DoctrineUserBundle\DependencyInjection\DoctrineUserExtension;
use Symfony\Component\Yaml\Parser;

class DoctrineUserExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    public function testDoctrineUserLoadModelWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\ExerciseUserBundle\Document\User', 'doctrine_user.model.user.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Document\Group', 'doctrine_user.model.group.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Document\Permission', 'doctrine_user.model.permission.class');
    }

    public function testDoctrineUserLoadRepositoryWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('doctrine_user.repository.user');
        $this->assertHasDefinition('doctrine_user.repository.group');
        $this->assertHasDefinition('doctrine_user.repository.permission');
    }

    public function testDoctrineUserLoadFormWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\DoctrineUserBundle\Form\UserForm', 'doctrine_user.form.user.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Form\GroupForm', 'doctrine_user.form.group.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Form\PermissionForm', 'doctrine_user.form.permission.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Form\SessionForm', 'doctrine_user.form.session.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Form\ChangePasswordForm', 'doctrine_user.form.change_password.class');

        $this->assertParameter('doctrine_user_user_form', 'doctrine_user.form.user.name');
        $this->assertParameter('doctrine_user_group_form', 'doctrine_user.form.group.name');
        $this->assertParameter('doctrine_user_permission_form', 'doctrine_user.form.permission.name');
        $this->assertParameter('doctrine_user_session_form', 'doctrine_user.form.session.name');
        $this->assertParameter('doctrine_user_change_password_form', 'doctrine_user.form.change_password.name');

        $this->assertHasDefinition('doctrine_user.form.user');
        $this->assertHasDefinition('doctrine_user.form.group');
        $this->assertHasDefinition('doctrine_user.form.permission');
        $this->assertHasDefinition('doctrine_user.form.session');
        $this->assertHasDefinition('doctrine_user.form.change_password');
    }

    public function testDoctrineUserLoadControllerWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\DoctrineUserBundle\Controller\UserController', 'doctrine_user.controller.user.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Controller\GroupController', 'doctrine_user.controller.group.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Controller\PermissionController', 'doctrine_user.controller.permission.class');
        $this->assertParameter('Bundle\DoctrineUserBundle\Controller\SessionController', 'doctrine_user.controller.session.class');

        $this->assertHasDefinition('doctrine_user.controller.user');
        $this->assertHasDefinition('doctrine_user.controller.group');
        $this->assertHasDefinition('doctrine_user.controller.permission');
        $this->assertHasDefinition('doctrine_user.controller.session');
    }

    public function testDoctrineUserLoadAuthWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\DoctrineUserBundle\Auth', 'doctrine_user.auth.class');
        $this->assertParameter('doctrine_user/auth/identifier', 'doctrine_user.auth.session_path');
        $this->assertHasDefinition('doctrine_user.auth');
    }

    public function testDoctrineUserLoadRememberMeWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('doctrine_user/remember_me', 'doctrine_user.remember_me.cookie_name');
        $this->assertParameter(2592000, 'doctrine_user.remember_me.lifetime');
    }

    public function testDoctrineUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'doctrine_user.confirmation_email.enabled');
        $this->assertParameter('webmaster@site.org', 'doctrine_user.confirmation_email.from_email');
        $this->assertParameter('DoctrineUserBundle:User:confirmationEmail', 'doctrine_user.confirmation_email.template');
    }

    public function testDoctrineUserLoadTemplateRendererWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('twig', 'doctrine_user.template_renderer');
    }

    /**
     * createEmptyConfiguration
     *
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new DoctrineUserExtension('testkernel');
        $config = $this->getEmptyConfig();
        $loader->configLoad($config, $this->configuration);
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
db_driver: odm
class:
    model:
        user: Bundle\ExerciseUserBundle\Document\User
        group: ~
        permission: ~
    form:
        user: ~
        group: ~
        permission: ~
        session: ~
        change_password: ~
    controller:
        user: ~
        group: ~
        permission: ~
        session: ~
auth:
    class: ~
    session_path: ~
remember_me:
    cookie_name: ~
    lifetime: ~
form_name:
    user: ~
    group: ~
    permission: ~
    session: ~
    change_password: ~
confirmation_email:
    enabled: ~
    from_email: ~
    template: ~
session_create_success_route: ~
template_renderer: ~
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    public function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    public function assertHasDefinition($id)
    {
        $this->assertTrue(($this->configuration->hasDefinition($id) ? : $this->configuration->hasAlias($id)));
    }

    public function tearDown()
    {
        unset($this->configuration);
    }

}
