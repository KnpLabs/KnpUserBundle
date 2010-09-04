<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PermissionRepositoryTest extends WebTestCase
{
    public function getPermissionRepository()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        return $kernel->getContainer()->get('doctrine_user.permission_repository');
    }

    public function testTimestampable()
    {
        $repo = $this->getPermissionRepository();
        $permission = $repo->findOneByName('permission1');
        
        $this->assertTrue($permission->getCreatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $permission->getCreatedAt());
        
        $this->assertTrue($permission->getUpdatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $permission->getUpdatedAt());
    }

    public function testFind()
    {
        $repo = $this->getPermissionRepository();
        $permission = $repo->findOneByName('permission1');

        $fetchedPermission = $repo->find($permission->getId());
        $this->assertSame($permission, $fetchedPermission);

        $nullPermission = $repo->find(0);
        $this->assertNull($nullPermission);
    }

    public function testFindOneByName()
    {
        $repo = $this->getPermissionRepository();
        $permission = $repo->findOneByName('permission1');

        $fetchedPermission = $repo->findOneByName($permission->getName());
        $this->assertEquals($permission->getName(), $fetchedPermission->getName());

        $nullPermission = $repo->findOneByName('thispermissionnamedoesnotexist----thatsprettycertain');
        $this->assertNull($nullPermission);
    }
}
