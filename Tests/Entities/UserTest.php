<?php

namespace Bundle\DoctrineUserBundle\Tests\Entities;

use Bundle\DoctrineUserBundle\Entities\User;

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../../Entities/User.php';

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testTimestampable()
    {
        $user = new User();
        
        $this->assertTrue($user->getCreatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getCreatedAt());
        
        $this->assertTrue($user->getUpdatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $user->getUpdatedAt());
    }

    public function testPassword()
    {
        $user = new User();
        $user->setPassword('changeme');

        $this->assertFalse($user->checkPassword('badpassword'));
        $this->assertTrue($user->checkPassword('changeme'));
    }

    public function testUsername()
    {
        $user = new User();
        $this->assertNull($user->getUsername());
        
        $user->setUsername('tony');
        $this->assertEquals('tony', $user->getUsername());
    }
}