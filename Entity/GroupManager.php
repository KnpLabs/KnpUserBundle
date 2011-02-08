<?php

namespace FOS\UserBundle\Entity;

use FOS\UserBundle\Model\GroupInterface;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\GroupManager as BaseGroupManager;

class GroupManager extends BaseGroupManager
{
    protected $em;
    protected $class;
    protected $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteGroup(GroupInterface $group)
    {
        $this->em->remove($group);
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
    public function findGroupBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findGroups()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function updateGroup(GroupInterface $group)
    {
        $this->em->persist($group);
        $this->em->flush();
    }
}
