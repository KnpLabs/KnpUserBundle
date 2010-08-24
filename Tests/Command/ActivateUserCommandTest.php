<?php

namespace Bundle\DoctrineUserBundle\Tests\Command;

use Bundle\DoctrineUserBundle\Tests\BaseDatabaseTest;
use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\Command\ActivateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

// Kernel creation required namespaces
use Symfony\Component\Finder\Finder;

class ActivateUserCommandTest extends BaseDatabaseTest
{
    public function testUserActivation()
    {
        $kernel = self::createKernel();
        $command = new ActivateUserCommand();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $tester = new ApplicationTester($application);

        $username = 'test_username';
        $password = 'test_password';
        $email    = 'test_email@email.org';

        $userRepo = $kernel->getContainer()->get('doctrine_user.user_repository');
        $userClass = $userRepo->getObjectClass();

        $user = new $userClass();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setIsActive(false);

        $userRepo->getObjectManager()->persist($user);
        $userRepo->getObjectManager()->flush();
        
        $this->assertFalse($user->getIsActive());

        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $kernel = self::createKernel();
        $userRepo = $kernel->getContainer()->get('doctrine_user.user_repository');
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->getIsActive());

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
