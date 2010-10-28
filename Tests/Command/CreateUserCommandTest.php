<?php

namespace Bundle\DoctrineUserBundle\Tests\Command;

use Bundle\DoctrineUserBundle\Test\WebTestCase;
use Bundle\DoctrineUserBundle\Model\User;
use Bundle\DoctrineUserBundle\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

class CreateUserCommandTest extends WebTestCase
{
    public function testUserCreation()
    {
        $kernel = $this->createKernel();
        $command = new CreateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
            'password' => $password,
            'email'    => $email,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userRepo = $this->getService('doctrine_user.repository.user');
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->checkPassword($password));
        $this->assertEquals($email, $user->getEmail());

        $userRepo->getObjectManager()->remove($user);
        $userRepo->getObjectManager()->flush();
    }

    public function testUserCreationWithOptions()
    {
        $kernel = $this->createKernel();
        $command = new CreateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);
        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';
        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            '--inactive' => true,
            '--super-admin' => true
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userRepo = $this->getService('doctrine_user.repository.user');
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->checkPassword($password));
        $this->assertEquals($email, $user->getEmail());
        $this->assertFalse($user->getIsActive());
        $this->assertTrue($user->getIsSuperAdmin());

        $userRepo->getObjectManager()->remove($user);
        $userRepo->getObjectManager()->flush();
    }

    public function tearDown()
    {
        $repo = $this->getService('doctrine_user.repository.user');
        $om = $repo->getObjectManager();
        if ($user = $repo->findOneByUsername('test_username')) {
            $om->remove($user);
        }
        $om->flush();
    }
}
