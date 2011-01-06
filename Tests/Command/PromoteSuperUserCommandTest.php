<?php

namespace Bundle\FOS\UserBundle\Tests\Command;

use Bundle\FOS\UserBundle\Test\WebTestCase;
use Bundle\FOS\UserBundle\Model\User;
use Bundle\FOS\UserBundle\Command\PromoteSuperAdminCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

class PromoteSuperAdminCommandTest extends WebTestCase
{
    public function testPromotion()
    {
        $kernel = $this->createKernel();
        $command = new PromoteSuperAdminCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $userManager = $kernel->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $userManager->updateUser($user);
        $this->assertFalse($user->hasRole('ROLE_SUPERADMIN'));
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userManager = $this->getService('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->hasRole('ROLE_SUPERADMIN'));

        $userManager->deleteUser($user);
    }

    public function tearDown()
    {
        $this->removeTestUser();
    }
}
