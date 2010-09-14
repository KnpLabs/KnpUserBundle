<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\Test\WebTestCase;

class PermissionRepositoryTest extends WebTestCase
{
    public function testTimestampable()
    {
        $repo = $this->getService('doctrine_user.permission_repository');
        $permission = $repo->findOneByName('permission1');
        
        $this->assertTrue($permission->getCreatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $permission->getCreatedAt());
        
        $this->assertTrue($permission->getUpdatedAt() instanceof \DateTime);
        $this->assertNotEquals(new \DateTime(), $permission->getUpdatedAt());
    }

    public function testFind()
    {
        $repo = $this->getService('doctrine_user.permission_repository');
        $permission = $repo->findOneByName('permission1');

        $fetchedPermission = $repo->find($permission->getId());
        $this->assertSame($permission, $fetchedPermission);

        $nullPermission = $repo->find(0);
        $this->assertNull($nullPermission);
    }

    public function testFindOneByName()
    {
        $repo = $this->getService('doctrine_user.permission_repository');
        $permission = $repo->findOneByName('permission1');

        $fetchedPermission = $repo->findOneByName($permission->getName());
        $this->assertEquals($permission->getName(), $fetchedPermission->getName());

        $nullPermission = $repo->findOneByName('thispermissionnamedoesnotexist----thatsprettycertain');
        $this->assertNull($nullPermission);
    }
}
