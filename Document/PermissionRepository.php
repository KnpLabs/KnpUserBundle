<?php

namespace Bundle\DoctrineUserBundle\Document;

use Bundle\DoctrineUserBundle\Model\PermissionRepositoryInterface;

class PermissionRepository extends ObjectRepository implements PermissionRepositoryInterface
{
    /**
     * @see PermissionRepositoryInterface::findOneByName
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }
}
