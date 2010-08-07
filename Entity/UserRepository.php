<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Entity;

use Doctrine\ORM as ORM;

class UserRepository extends ORM\EntityRepository implements UserRepositoryInterface
{
    /**
     * Create a new user
     * @param string  $username       username
     * @param string  $password       password
     * @param boolean $isActive      is the user active
     * @param boolean $isSuperAdmin is the user a super admin
     */
    public function createUser($username, $password, $isActive = true, $isSuperAdmin = false)
    {
        $user = new User();

        $user->setUsername($username);
        $user->setPassword($password);
        $user->setIsActive($isActive);
        $user->setIsSuperAdmin($isSuperAdmin);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Find a user by its username and password
     * @param   string  $username
     * @param   string  $password
     * @return  User or null if user does not exist
     */
    public function findOneByUsernameAndPassword($username, $password)
    {
        $user = $this->findOneByUsername($username);
        
        if($user && $user->checkPassword($password))
        {
            return $user;
        }
    }

    /**
     * Find a user by its username
     * @param   string  $username
     * @return  User or null if user does not exist
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

}

