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
     * Create a new user
     * @param  string  $username       username
     * @param  string  $email          email
     * @param  string  $password       password
     * @param  boolean $isActive      is the user active
     * @param  boolean $isSuperAdmin is the user a super admin
     * @return User
     */
    public function createUser($username, $email, $password, $isActive = true, $isSuperAdmin = false);

    /**
     * Find a user by its username
     * @param  string  $username
     * @return User or null if user does not exist
     */
    public function findOneByUsername($username);

    /**
     * Find a user by its email
     * @param  string  $email
     * @return User or null if user does not exist
     */
    public function findOneByEmail($email);

    /**
     * Find a user by its username or email
     * @param  string  $usernameOrEmail
     * @return User or null if user does not exist
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

    /**
     * Get the identifier property of the User 
     * 
     * @return string
     */
    public function getObjectIdentifier();
}
