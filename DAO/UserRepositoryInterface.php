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
     * Find a user by its id
     * @param   mixed  $id
     * @return  User or null if user does not exist
     */
    public function findOneById($id);

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    public function findOneByUsername($username);
}
