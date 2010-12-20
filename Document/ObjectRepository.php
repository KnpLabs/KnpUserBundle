<?php

namespace Bundle\FOS\UserBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Bundle\FOS\UserBundle\Model\RepositoryInterface;

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

    public function createObjectInstance()
    {
        $className = $this->getObjectClass();
        return new $className();
    }
}
