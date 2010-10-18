<?php

namespace Bundle\DoctrineUserBundle\Document;

use Bundle\DoctrineUserBundle\Model\GroupRepositoryInterface;

class GroupRepository extends ObjectRepository implements GroupRepositoryInterface
{
    /**
     * @see GroupRepositoryInterface::findOneByName
     */
    public function findOneByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }
}
