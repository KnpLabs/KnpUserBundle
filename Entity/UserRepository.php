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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * Create a new user
     * @param string  $username       username
     * @param string  $password       password
     * @param boolean $isActive      is the user active
     * @param boolean $isSuperAdmin is the user a super admin
     */
    public function createUser($username, $email, $password, $isActive = true, $isSuperAdmin = false)
    {
        $user = $this->getObjectClass();

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setIsActive($isActive);
        $user->setIsSuperAdmin($isSuperAdmin);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @see UserRepositoryInterface::findOneByUsername
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(array('username' => $username));
    }

    /**
     * @see UserRepositoryInterface::findOneByEmail
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(array('email' => $email));
    }

    /**
     * @see UserRepositoryInterface::findOneByUsernameOrEmail
     */
    public function findOneByUsernameOrEmail($usernameOrEmail)
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.username = :string')
                ->orWhere('u.email = :string')
                ->setParameter('string', $usernameOrEmail)
                ->getQuery()
                ->getSingleResult();
        }
        catch(NoResultException $e) {
            return null;
        }
    }

    /**
     * @see UserRepositoryInterface::getObjectManager
     */
    public function getObjectManager()
    {
        return $this->getEntityManager();
    }

    /**
     * @see UserRepositoryInterface::getObjectClass
     */
    public function getObjectClass()
    {
        return $this->getEntityName();
    }

    /**
     * @see UserRepositoryInterface::getObjectIdentifier
     */
    public function getObjectIdentifier()
    {
        return reset($this->getClassMetadata()->identifier);
    }
}
