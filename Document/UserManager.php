<?php

namespace Bundle\FOS\UserBundle\Document;

use Bundle\FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\DocumentManager;
use Bundle\FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    protected $dm;
    protected $repository;
    protected $class;

    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->namespace.'\\'.$metadata->name;
    }

    public function deleteUser(BaseUser $user)
    {
        $this->dm->remove($user);
        $this->dm->flush();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function updateUser(BaseUser $user)
    {
        $this->dm->persist($user);
        $this->dm->flush();
    }
}