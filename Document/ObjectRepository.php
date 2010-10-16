<?php

namespace Bundle\DoctrineUserBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Bundle\DoctrineUserBundle\Model\RepositoryInterface;

abstract class ObjectRepository extends DocumentRepository implements RepositoryInterface
{
    /**
     * @see RepositoryInterface::getObjectManager
     */
    public function getObjectManager()
    {
        return $this->getDocumentManager();
    }

    /**
     * @see RepositoryInterface::getObjectClass
     */
    public function getObjectClass()
    {
        return $this->getDocumentName();
    }

    /**
     * @see RepositoryInterface::getObjectIdentifier
     */
    public function getObjectIdentifier()
    {
        return $this->getClassMetadata()->identifier;
    }
}
