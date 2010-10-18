<?php

namespace Bundle\DoctrineUserBundle\Model;

interface GroupRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a group by its name
     *
     * @param string $name
     * @return Group or null if the group was not found
     */
    public function findOneByName($name);
}
