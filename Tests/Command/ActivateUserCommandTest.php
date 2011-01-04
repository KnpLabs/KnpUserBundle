<?php

namespace Bundle\FOS\UserBundle\Tests\Command;

use Bundle\FOS\UserBundle\Test\WebTestCase;
use Bundle\FOS\UserBundle\Model\User;
use Bundle\FOS\UserBundle\Command\ActivateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

class ActivateUserCommandTest extends WebTestCase
{
    public function testUserActivation()
    {
        $kernel = $this->createKernel();
        $command = new ActivateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);

        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';

        $userManager = $this->getService('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setEnabled(false);

        $userManager->updateUser($user);

        $this->assertFalse($user->isEnabled());

        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $this->getService('doctrine.orm.default_entity_manager')->clear();

        $userManager = $this->getService('fos_user.user_manager');
        $user = $userManager->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->isEnabled());

        $userManager->updateUser($user);
    }

    public function tearDown()
    {
        $this->removeTestUser();
    }
}
