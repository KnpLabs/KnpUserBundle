<?php

namespace Bundle\FOS\UserBundle\Entity;

use Bundle\FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;
use Bundle\FOS\UserBundle\Model\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    protected $em;
    protected $class;
    protected $repository;

    public function __construct($encoder, $algorithm, EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->namespace.'\\'.$metadata->name;

        parent::__construct($encoder, $algorithm);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(UserInterface $user)
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
    public function updateUser(UserInterface $user)
    {
        $this->updatePassword($user);

        $this->em->persist($user);
        $this->em->flush();
    }
}
