<?php

namespace Bundle\FOS\UserBundle\Tests\Command;

use Bundle\FOS\UserBundle\Test\WebTestCase;
use Bundle\FOS\UserBundle\Model\User;
use Bundle\FOS\UserBundle\Command\DemoteSuperAdminCommand;
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

        $userRepo = $this->getService('fos_user.repository.user');
        $userClass = $userRepo->getObjectClass();

        $user = $userRepo->createObjectInstance();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->addRole('ROLE_SUPERADMIN');

        $userRepo->getObjectManager()->persist($user);
        $userRepo->getObjectManager()->flush();

        $this->assertTrue($user->hasRole('ROLE_SUPERADMIN'));

        $tester->run(array(
            'command'  => $command->getFullName(),
            'username' => $username,
        ), array('interactive' => false, 'decorated' => false, 'verbosity' => Output::VERBOSITY_VERBOSE));

        $userRepo = $this->getService('fos_user.repository.user');
        $userRepo->getObjectManager()->clear();
        $user = $userRepo->findOneByUsername($username);

        $this->assertTrue($user instanceof User);
        $this->assertFalse($user->hasRole('ROLE_SUPERADMIN'));

        $userRepo->getObjectManager()->remove($user);
        $userRepo->getObjectManager()->flush();
    }

    public function tearDown()
    {
        $repo = $this->getService('fos_user.repository.user');
        $om = $repo->getObjectManager();
        if ($user = $repo->findOneByUsername('test_username')) {
            $om->remove($user);
        }
        $om->flush();
    }
}
