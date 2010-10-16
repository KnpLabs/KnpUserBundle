<?php

namespace Bundle\DoctrineUserBundle\Tests\Model;

use Bundle\DoctrineUserBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    public function testTimestampable()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');
        
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $user->getCreatedAt());
        
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $user->getUpdatedAt());
    }

    public function testFind()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');

        $fetchedUser = $repo->find($user->getId());
        $this->assertSame($user, $fetchedUser);

        $nullUser = $repo->find(0);
        $this->assertNull($nullUser);
    }

    public function testFindOneByUsername()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');

        $fetchedUser = $repo->findOneByUsername($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $nullUser = $repo->findOneByUsername('thisusernamedoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    public function testFindOneByEmail()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');

        $fetchedUser = $repo->findOneByEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $nullUser = $repo->findOneByEmail('thisemaildoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    public function testFindOneByUsernameOrEmail()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');
        $user2 = $repo->findOneByUsername('user1');

        $fetchedUser = $repo->findOneByUsernameOrEmail($user->getUsername());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $repo->findOneByUsernameOrEmail($user2->getUsername());
        $this->assertEquals($user2->getUsername(), $fetchedUser->getUsername());

        $fetchedUser = $repo->findOneByUsernameOrEmail($user->getEmail());
        $this->assertEquals($user->getEmail(), $fetchedUser->getEmail());

        $fetchedUser = $repo->findOneByUsernameOrEmail($user2->getEmail());
        $this->assertEquals($user2->getEmail(), $fetchedUser->getEmail());

        $nullUser = $repo->findOneByUsernameOrEmail('thisemaildoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);
    }

    public function testFindOneByRememberMeToken()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');

        $fetchedUser = $repo->findOneByRememberMeToken($user->getRememberMeToken());
        $this->assertEquals($user->getUsername(), $fetchedUser->getUsername());

        $nullUser = $repo->findOneByRememberMeToken('thistokendoesnotexist----thatsprettycertain');
        $this->assertNull($nullUser);

        $nullUser = $repo->findOneByRememberMeToken('');
        $this->assertNull($nullUser);
    }

    public function testCompareUsers()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');
        $user2 = $repo->findOneByUsername('user1');
        
        $this->assertTrue($user == $user);
        $this->assertTrue($user->is($user));
        $this->assertFalse($user == $user2);
        $this->assertFalse($user->is($user2));
        $this->assertFalse($user2->is($user));

        $this->assertTrue($repo->findOneByUsername('user1') == $repo->findOneByUsername('user1'));
        $this->assertTrue($repo->findOneByUsername('user1')->is($repo->findOneByUsername('user1')));
        $this->assertFalse($repo->findOneByUsername('user1') == $repo->findOneByUsername('user2'));
        $this->assertFalse($repo->findOneByUsername('user1')->is($repo->findOneByUsername('user2')));
    }
}
