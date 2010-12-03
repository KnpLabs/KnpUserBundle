<?php

namespace Bundle\DoctrineUserBundle\Tests\Model;

use Bundle\DoctrineUserBundle\Model\User as AbstractUser;

class User extends AbstractUser
{
}

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = new User('sha1');
        $this->assertNull($user->getUsername());

        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }

    public function testEmail()
    {
        $user = new User('sha1');
        $this->assertNull($user->getEmail());

        $user->setEmail('tony@mail.org');
        $this->assertEquals('tony@mail.org', $user->getEmail());
    }

    public function testRenewRememberMeToken()
    {
        $user = new User('sha1');
        $rmt = $user->getRememberMeToken();
        $this->assertNotNull($rmt);
        $user->renewRememberMeToken();
        $this->assertNotEquals($rmt, $user->getRememberMeToken());
    }
}
