<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Bundle\DoctrineUserBundle\DAO\PermissionRepositoryInterface;

class PermissionRepository extends EntityRepository implements PermissionRepositoryInterface
{
    /**
     * @see PermissionRepositoryInterface::findOneByName
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }

    /**
     * @see PermissionRepositoryInterface::getObjectManager
     */
    public function getObjectManager()
    {
        return $this->getEntityManager();
    }

    /**
     * @see PermissionRepositoryInterface::getObjectClass
     */
    public function getObjectClass()
    {
        return $this->getEntityName();
    }

    /**
     * @see PermissionRepositoryInterface::getObjectIdentifier
     */
    public function getObjectIdentifier()
    {
        return reset($this->getClassMetadata()->identifier);
    }
}
