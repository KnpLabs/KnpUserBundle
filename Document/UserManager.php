<?php

namespace Bundle\FOS\UserBundle\Document;

use Bundle\FOS\UserBundle\Model\UserInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Bundle\FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    protected $dm;
    protected $repository;
    protected $class;

    public function __construct($encoder, $algorithm, DocumentManager $dm, $class)
    {
        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->namespace.'\\'.$metadata->name;

        parent::__construct($encoder, $algorithm);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
    {
        $this->dm->remove($user);
        $this->dm->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function updateUser(UserInterface $user)
    {
        $this->updatePassword($user);

        $this->dm->persist($user);
        $this->dm->flush();
    }
}
