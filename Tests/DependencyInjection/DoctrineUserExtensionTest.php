<?php

namespace Bundle\FOS\UserBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bundle\FOS\UserBundle\DependencyInjection\UserExtension;
use Symfony\Component\Yaml\Parser;

class UserExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $configuration;

    public function testUserLoadModelClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\ExerciseUserBundle\Document\User', 'fos_user.model.user.class');
    }

    public function testUserLoadModelClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.model.user.class');
    }

    public function testUserLoadRepositoryClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.repository.user');
    }

    public function testUserLoadRepositoryClass()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.repository.user');
    }

    public function testUserLoadFormClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\FOS\UserBundle\Form\UserForm', 'fos_user.form.user.class');
        $this->assertParameter('Bundle\FOS\UserBundle\Form\ChangePasswordForm', 'fos_user.form.change_password.class');
    }

    public function testUserLoadFormClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.form.user.class');
        $this->assertParameter('change_password', 'fos_user.form.change_password.class');
    }

    public function testUserLoadFormNameWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('fos_user_user_form', 'fos_user.form.user.name');
        $this->assertParameter('fos_user_change_password_form', 'fos_user.form.change_password.name');
    }

    public function testUserLoadFormName()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.form.user.name');
        $this->assertParameter('change_password', 'fos_user.form.change_password.name');
    }

    public function testUserLoadFormServiceWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.change_password');
    }

    public function testUserLoadFormService()
    {
        $this->createFullConfiguration();

        $this->assertHasDefinition('fos_user.form.user');
        $this->assertHasDefinition('fos_user.form.change_password');
    }

    public function testUserLoadControllerClassWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('Bundle\FOS\UserBundle\Controller\UserController', 'fos_user.controller.user.class');
        $this->assertParameter('Bundle\FOS\UserBundle\Controller\SecurityController', 'fos_user.controller.security.class');
    }

    public function testUserLoadControllerClass()
    {
        $this->createFullConfiguration();

        $this->assertParameter('user', 'fos_user.controller.user.class');
        $this->assertParameter('security', 'fos_user.controller.security.class');
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
        $this->assertHasDefinition('fos_user.controller.security');
    }

    public function testUserLoadConfirmationEmailWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(false, 'fos_user.confirmation_email.enabled');
        $this->assertParameter('webmaster@site.org', 'fos_user.confirmation_email.from_email');
        $this->assertParameter('FOS\UserBundle:User:confirmationEmail', 'fos_user.confirmation_email.template');
    }

    public function testUserLoadConfirmationEmail()
    {
        $this->createFullConfiguration();

        $this->assertParameter('enabled', 'fos_user.confirmation_email.enabled');
        $this->assertParameter('from_email', 'fos_user.confirmation_email.from_email');
        $this->assertParameter('template', 'fos_user.confirmation_email.template');
    }

    public function testUserLoadTemplateConfigWithDefaults()
    {
        $this->createEmptyConfiguration();

        $this->assertParameter('twig', 'fos_user.template.renderer');
        $this->assertParameter('TwigBundle::form.twig', 'fos_user.template.theme');
    }

    public function testUserLoadTemplateConfig()
    {
        $this->createFullConfiguration();

        $this->assertParameter('renderer', 'fos_user.template.renderer');
        $this->assertParameter('theme', 'fos_user.template.theme');
    }

    /**
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new UserExtension('testkernel');
        $config = $this->getEmptyConfig();
        $loader->configLoad($config, $this->configuration);
        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    protected function createFullConfiguration()
    {
        $this->configuration = new ContainerBuilder();
        $loader = new UserExtension('testkernel');
        $config = $this->getFullConfig();
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
password_encoder: sha1
class:
    model:
        user: Bundle\ExerciseUserBundle\Document\User
    form:
        user: ~
        change_password: ~
    controller:
        user: ~
        security: ~
form_name:
    user: ~
    change_password: ~
confirmation_email:
    enabled: ~
    from_email: ~
    template: ~
template:
    renderer: ~
    theme: ~
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    protected function getFullConfig()
    {
        $config = $this->getEmptyConfig();
        array_walk_recursive($config, function(&$item, $key) {
            if (!is_array($item)) {
                $item = $key;
            }
        });
        $config['db_driver'] = 'orm';

        return $config;
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
