<?php

namespace Bundle\DoctrineUserBundle\Tests\Command;

use Bundle\DoctrineUserBundle\Tests\BaseDatabaseTest;
use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Components\Console\Output\Output;
use Symfony\Components\Console\Tester\ApplicationTester;

// Kernel creation required namespaces
use Symfony\Components\Finder\Finder;

class CreateUserCommandTest extends BaseDatabaseTest
{
    public function testUserCreation()
    {
        $kernel = self::createKernel();
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

        $kernel = self::createKernel();
        $userRepo = $kernel->getContainer()->get('doctrine_user.user_repository');
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->checkPassword($password));
        $this->assertEquals($email, $user->getEmail());

        $userRepo->getObjectManager()->remove($user);
        $userRepo->getObjectManager()->flush();
    }

    static public function tearDownAfterClass()
    {
        $userRepo = self::createKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $objectManager = $userRepo->getObjectManager();
        if($object = $userRepo->findOneByUsername('test_username')) {
            $objectManager->remove($object);
        }
        $objectManager->flush();
    }
}
