<?php

namespace Bundle\DoctrineUserBundle\Tests\DAO;

use Bundle\DoctrineUserBundle\Tests\BaseDatabaseTest;
use Bundle\DoctrineUserBundle\DAO\Permission;
use Bundle\DoctrineUserBundle\DAO\PermissionRepositoryInterface;

// Kernel creation required namespaces
use Symfony\Components\Finder\Finder;

class PermissionRepositoryTest extends BaseDatabaseTest
{
    public function testGetPermissionRepo()
    {
        $permissionRepo = self::createKernel()->getContainer()->getDoctrineUser_PermissionRepositoryService();
        $this->assertTrue($permissionRepo instanceof PermissionRepositoryInterface);

        return $permissionRepo;
    }

    /**
     * @depends testGetPermissionRepo
     */
    public function testCreateNewPermission(PermissionRepositoryInterface $permissionRepo)
    {
        $objectManager = $permissionRepo->getObjectManager();

        $permissionClass = $permissionRepo->getObjectClass();
        $permission = new $permissionClass();
        $permission->setName('harry_test');
        $permission->setDescription('permission description');
        $objectManager->persist($permission);

        $permission2 = new $permissionClass();
        $permission2->setName('harry_test2');
        $permission2->setDescription('permission description 2');
        $objectManager->persist($permission2);

        $objectManager->flush();

        $this->assertNotNull($permission->getId());
        $this->assertNotNull($permission2->getId());

        return array($permissionRepo, $permission, $permission2);
    }

    /**
     * @depends testCreateNewPermission
     */
    public function testTimestampable(array $dependencies)
    {
        list($permissionRepo, $permission) = $dependencies;
        
        $this->assertTrue($permission->getCreatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $permission->getCreatedAt());
        
        $this->assertTrue($permission->getUpdatedAt() instanceof \DateTime);
        $this->assertEquals(new \DateTime(), $permission->getUpdatedAt());
    }

    /**
     * @depends testCreateNewPermission
     */
    public function testFind(array $dependencies)
    {
        list($permissionRepo, $permission) = $dependencies;

        $fetchedPermission = $permissionRepo->find($permission->getId());
        $this->assertSame($permission, $fetchedPermission);

        $nullPermission = $permissionRepo->find(0);
        $this->assertNull($nullPermission);
    }

    /**
     * @depends testCreateNewPermission
     */
    public function testFindOneByName(array $dependencies)
    {
        list($permissionRepo, $permission) = $dependencies;

        $fetchedPermission = $permissionRepo->findOneByName($permission->getName());
        $this->assertEquals($permission->getName(), $fetchedPermission->getName());

        $nullPermission = $permissionRepo->findOneByName('thispermissionnamedoesnotexist----thatsprettycertain');
        $this->assertNull($nullPermission);
    }

    static public function tearDownAfterClass()
    {
        $permissionRepo = self::createKernel()->getContainer()->getDoctrineUser_PermissionRepositoryService();
        $objectManager = $permissionRepo->getObjectManager();
        foreach(array('harry_test', 'harry_test2') as $permissionname) {
            if($object = $permissionRepo->findOneByName($permissionname)) {
                $objectManager->remove($object);
            }
        }
        $objectManager->flush();
    }
}
