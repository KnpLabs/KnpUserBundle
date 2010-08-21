<?php

namespace Bundle\DoctrineUserBundle\DAO;

interface PermissionRepositoryInterface
{
    /**
     * Find a permission by its name
     *
     * @param string $name
     * @return Permission or null if the permission was not found
     */
    public function findOneByName($name);

    /**
     * Get the Entity manager or the Document manager, depending on the db driver
     *
     * @return mixed
     **/
    public function getObjectManager();

    /**
     * Get the class of the User Entity or Document, depending on the db driver
     *
     * @return string a model fully qualified class name
     **/
    public function getObjectClass();

    /**
     * Get the identifier property of the Permission
     *
     * @return string
     */
    public function getObjectIdentifier();
}
