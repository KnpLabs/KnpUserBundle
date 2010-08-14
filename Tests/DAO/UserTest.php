<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\DAO\User as AbstractUser;

class User extends AbstractUser
{
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
}
