<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

interface PermissionRepositoryInterface
{
    /**
     * Find a permission by its name
     * @param   string  $name
     * @return  Permission or null if name does not exist
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
     * Get the identifier property of the User 
     * 
     * @return string
     */
    public function getObjectIdentifier();
}
