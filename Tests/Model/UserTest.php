<?php

namespace Bundle\DoctrineUserBundle\Tests\Model;

use Bundle\DoctrineUserBundle\Model\User as AbstractUser;

class User extends AbstractUser
{
    public function getGroupNames() { return array(); }
    public function getPermissionNames() { return array(); }
    public function getAllPermissionNames() { return array(); }
}

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testUsername()
    {
        $user = new User();
        $this->assertNull($user->getUsername());
        
        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }

    public function testEmail()
    {
        $user = new User();
        $this->assertNull($user->getEmail());
        
        $user->setEmail('tony@mail.org');
        $this->assertEquals('tony@mail.org', $user->getEmail());
    }

    public function testCheckPassword()
    {
        $user = new User();
        $user->setPassword('changeme');

        $this->assertFalse($user->checkPassword('badpassword'));
        $this->assertTrue($user->checkPassword('changeme'));
    }

    public function testRenewRememberMeToken()
    {
        $user = new User();
        $rmt = $user->getRememberMeToken();
        $this->assertNotNull($rmt);
        $user->renewRememberMeToken();
        $this->assertNotEquals($rmt, $user->getRememberMeToken());
    }
}
