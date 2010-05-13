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

namespace Bundle\DoctrineUserBundle\Entities;

use Doctrine\ORM as ORM;

class UserRepository extends ORM\EntityRepository
{

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
        try
        {
            return $this->createQueryBuilder('e')
            ->where('e.username = :username')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('username', $username)
            ->getSingleResult();
        }
        catch(ORM\NoResultException $e)
        {
            return null;
        }
    }

}

