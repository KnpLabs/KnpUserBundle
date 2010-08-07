<?php

/**
 * This file is part of the Symfony framework.
 *
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
     * @param string  $username       username
     * @param string  $password       password
     * @param boolean $isActive      is the user active
     * @param boolean $isSuperAdmin is the user a super admin
     * @return  BaseUser The created user
     */
    public function createUser($username, $password, $isActive = true, $isSuperAdmin = false);

    /**
     * Find a user by its username and password
     * @param   string  $username
     * @param   string  $password
     * @return  BaseUser or null if user does not exist
     */
    public function findOneByUsernameAndPassword($username, $password);

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  BaseUser or null if user does not exist
     */
    public function findOneByUsername($username);
}

