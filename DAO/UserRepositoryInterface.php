<?php

/**
 * (c) Gordon Franke <info@nevalon.de>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

interface UserRepositoryInterface
{
    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    public function findOneByUsername($username);

    /**
     * Find a user by its email
     * @param   string  $email
     * @return  User or null if user does not exist
     */
    public function findOneByEmail($email);

    /**
     * Find a user by its username or email
     * @param   string  $usernameOrEmail
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameOrEmail($usernameOrEmail);

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
}
