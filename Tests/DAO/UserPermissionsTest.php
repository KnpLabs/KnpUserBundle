<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\Test\WebTestCase;
use Doctrine\Common\Collections\Collection;

class UserPermissionsTest extends WebTestCase
{
    public function testGetEmptyPermissions()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('admin');
        $this->assertTrue($user->getPermissions() instanceof Collection);
        $this->assertEquals(0, count($user->getPermissions()));
    }

    public function testGetPermissions()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('user1');
        $this->assertTrue($user->getPermissions() instanceof Collection);
        $this->assertEquals(2, count($user->getPermissions()));
    }

    public function testGetAllPermissionsReturnGroupPermissions()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('user2');
        $this->assertTrue($user->getAllPermissions() instanceof Collection);
        $this->assertEquals(3, count($user->getAllPermissions()));
    }

    public function testGetAllPermissionsReturnGroupPermissionsAndPermissions()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('user1');
        $this->assertTrue($user->getAllPermissions() instanceof Collection);
        $this->assertEquals(5, count($user->getAllPermissions()));
    }

    public function testHasPermission()
    {
        $repo = $this->getService('doctrine_user.user_repository');
        $user = $repo->findOneByUsername('user1');
        $this->assertTrue($user->hasPermission('permission1'));
        $this->assertTrue($user->hasPermission('permission3'));
        $this->assertFalse($user->hasPermission('non-existing-permission-!'));
    }

}
