<?php

namespace Bundle\FOS\UserBundle\Entity;

use Bundle\FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;
use Bundle\FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    protected $em;
    protected $class;
    protected $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->namespace.'\\'.$metadata->name;
    }

    public function deleteUser(BaseUser $user)
    {
        $this->em->remove($user);
        $this->em->flush();
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
    public function updateUser(BaseUser $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}