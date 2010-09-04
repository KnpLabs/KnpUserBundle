<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserPermissionsTest extends WebTestCase
{
    public function getUserRepository()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        return $kernel->getContainer()->get('doctrine_user.user_repository');
    }

    public function testGetEmptyPermissions()
    {
        $repo = $this->getUserRepository();
        $user = $repo->findOneByUsername('admin');
        $this->assertEquals(0, count($user->getPermissions()));
    }

    public function testGetPermissions()
    {
        $repo = $this->getUserRepository();
        $user = $repo->findOneByUsername('user1');
        $this->assertEquals(2, count($user->getPermissions()));
    }

    public function testGetAllPermissionsReturnGroupPermissions()
    {
        $repo = $this->getUserRepository();
        $user = $repo->findOneByUsername('user2');
        $this->assertEquals(3, count($user->getAllPermissions()));
    }

    public function testGetAllPermissionsReturnGroupPermissionsAndPermissions()
    {
        $repo = $this->getUserRepository();
        $user = $repo->findOneByUsername('user1');
        $this->assertEquals(5, count($user->getAllPermissions()));
    }

    public function testHasPermission()
    {
        $repo = $this->getUserRepository();
        $user = $repo->findOneByUsername('user1');
        $this->assertTrue($user->hasPermission('permission1'));
        $this->assertTrue($user->hasPermission('permission3'));
        $this->assertFalse($user->hasPermission('non-existing-permission-!'));
    }

}
