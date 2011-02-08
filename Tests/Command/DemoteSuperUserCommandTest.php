<?php

namespace FOS\UserBundle\Tests\Command;

use FOS\UserBundle\Test\WebTestCase;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Command\DemoteSuperAdminCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

class DemoteSuperAdminCommandTest extends WebTestCase
{
    public function testPromotion()
    {
        $kernel = $this->createKernel();
        $command = new DemoteSuperAdminCommand();
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
        $user->addRole('ROLE_SUPERADMIN');

        $userManager->updateUser($user);

        $this->assertTrue($user->hasRole('ROLE_SUPERADMIN'));

        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $this->getService('doctrine.orm.default_entity_manager')->clear();

        $userManager = $this->getService('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertFalse($user->hasRole('ROLE_SUPERADMIN'));

        $userManager->deleteUser($user);
    }

    protected function tearDown()
    {
        $this->removeTestUser();
    }
}
