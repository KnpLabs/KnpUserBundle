<?php

namespace Bundle\DoctrineUserBundle\Model;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a permission by its name
     *
     * @param string $name
     * @return Permission or null if the permission was not found
     */
    public function findOneByName($name);
}
