<?php

namespace Bundle\FOS\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Bundle\FOS\UserBundle\Model\RepositoryInterface;

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

    public function createObjectInstance()
    {
        $className = $this->getObjectClass();
        return new $className();
    }
}
