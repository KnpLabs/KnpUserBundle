<?php

namespace Bundle\DoctrineUserBundle\Tests\Model;

use Bundle\DoctrineUserBundle\Model\Permission as AbstractPermission;

class Permission extends AbstractPermission
{
}

class PermissionTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $permission = new Permission();
        $this->assertNull($permission->getName());
        
        $permission->setName('deletion');
        $this->assertEquals('deletion', $permission->getName());
    }

    public function testDescription()
    {
        $permission = new Permission();
        $this->assertNull($permission->getDescription());
        
        $permission->setDescription('Delete things');
        $this->assertEquals('Delete things', $permission->getDescription());
    }
}
