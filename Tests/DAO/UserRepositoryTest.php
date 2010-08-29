<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\Tests\BaseDatabaseTest;
use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;

// Kernel creation required namespaces
use Symfony\Component\Finder\Finder;

class UserRepositoryTest extends BaseDatabaseTest
{
    public function testGetUserRepo()
    {
        $userRepo = self::createKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $this->assertTrue($userRepo instanceof UserRepositoryInterface);

        return $userRepo;
    }

    /**
     * @depends testGetUserRepo
     */
    public function testCreateNewUser(UserRepositoryInterface $userRepo)
    {
        $objectManager = $userRepo->getObjectManager();

        $userClass = $userRepo->getObjectClass();
        $user = new $userClass();
        $user->setUserName('harry_test');
        $user->setEmail('harry@mail.org');
        $user->setPassword('changeme');
        $objectManager->persist($user);

        $user2 = new $userClass();
        $user2->setUserName('harry_test2');
        $user2->setEmail('harry2@mail.org');
        $user2->setPassword('changeme2');
        $objectManager->persist($user2);

        $objectManager->flush();

        $this->assertNotNull($user->getId());
        $this->assertNotNull($user2->getId());

        return array($userRepo, $user, $user2);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testTimestampable(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;
        
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getCreatedAt());
        
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getUpdatedAt());
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFind(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->find($user->getId());
        $this->assertSame($user, $fetchedUser);

        $nullUser = $userRepo->find(0);
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByUsername(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->findOneByUsername($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $nullUser = $userRepo->findOneByUsername('thisusernamedoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByEmail(array $dependencies)
    {
        list($userRepo, $user) = $dependencies;

        $fetchedUser = $userRepo->findOneByEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $nullUser = $userRepo->findOneByEmail('thisemaildoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testFindOneByUsernameOrEmail(array $dependencies)
    {
        list($userRepo, $user, $user2) = $dependencies;

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user2->getUsername());
        $this->assertEquals($user2->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $fetchedUser = $userRepo->findOneByUsernameOrEmail($user2->getEmail());
        $this->assertEquals($user2->getEmail(), $fetchedUser->getEmail());

        $nullUser = $userRepo->findOneByUsernameOrEmail('thisemaildoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    /**
     * @depends testCreateNewUser
     */
    public function testCompareUsers(array $dependencies)
    {
        list($userRepo, $user, $user2) = $dependencies;
        
        $this->assertTrue($user == $user);
        $this->assertTrue($user->is($user));
        $this->assertFalse($user == $user2);
        $this->assertFalse($user->is($user2));
        $this->assertFalse($user2->is($user));

        $this->assertTrue($userRepo->findOneByUsername('harry_test') == $userRepo->findOneByUsername('harry_test'));
        $this->assertTrue($userRepo->findOneByUsername('harry_test')->is($userRepo->findOneByUsername('harry_test')));
        $this->assertFalse($userRepo->findOneByUsername('harry_test') == $userRepo->findOneByUsername('harry_test2'));
        $this->assertFalse($userRepo->findOneByUsername('harry_test')->is($userRepo->findOneByUsername('harry_test2')));
    }

    static public function tearDownAfterClass()
    {
        $userRepo = self::createKernel()->getContainer()->getDoctrineUser_UserRepositoryService();
        $objectManager = $userRepo->getObjectManager();
        foreach(array('harry_test', 'harry_test2') as $username) {
            if($object = $userRepo->findOneByUsername($username)) {
                $objectManager->remove($object);
            }
        }
        $objectManager->flush();
    }
}
