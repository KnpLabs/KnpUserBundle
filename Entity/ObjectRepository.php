<?php

namespace Bundle\DoctrineUserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Bundle\DoctrineUserBundle\Model\RepositoryInterface;

abstract class ObjectRepository extends EntityRepository implements RepositoryInterface
{
    /**
     * @see RepositoryInterface::getObjectManager
     */
    public function getObjectManager()
    {
        return $this->getEntityManager();
    }

    /**
     * @see RepositoryInterface::getObjectClass
     */
    public function getObjectClass()
    {
        return $this->getEntityName();
    }

    /**
     * @see RepositoryInterface::getObjectIdentifier
     */
    public function getObjectIdentifier()
    {
        return reset($this->getClassMetadata()->identifier);
    }
}
