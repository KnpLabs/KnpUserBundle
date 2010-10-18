<?php

namespace Bundle\DoctrineUserBundle\Entity;

use Doctrine\ORM\NoResultException;
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
