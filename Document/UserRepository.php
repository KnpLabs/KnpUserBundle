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

namespace Bundle\DoctrineUserBundle\Document;

use Bundle\DoctrineUserBundle\Model\UserRepositoryInterface;
use Symfony\Component\Security\User\UserProviderInterface;
use Symfony\Component\Security\Exception\UsernameNotFoundException;

class UserRepository extends ObjectRepository implements UserRepositoryInterface, UserProviderInterface
{
    /**
     * @see UserRepositoryInterface::findOneByUsername
     */
    public function findOneByUsername($username)
    {
        return $this->findOneBy(array('usernameLower' => strtolower($username)));
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param  string $username The username
     * @return AccountInterface A user instance
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findOneByUsername($username);

        if(!$user) {
            throw new UsernameNotFoundException(sprintf('The user "%s" does not exist', $username));
        }

        return $user;
    }

    /**
     * @see UserRepositoryInterface::findOneByEmail
     */
    public function findOneByEmail($email)
    {
        return $this->findOneBy(array('email' => strtolower($email)));
    }

    /**
     * @see UserRepositoryInterface::findOneByUsernameOrEmail
     */
    public function findOneByUsernameOrEmail($usernameOrEmail)
    {
        if ($this->isValidEmail($usernameOrEmail)) {
            return $this->findOneByEmail($usernameOrEmail);
        }

        return $this->findOneByUsername($usernameOrEmail);
    }

    /**
     * @see UserRepositoryInterface::findOneByConfirmationToken
     */
    public function findOneByConfirmationToken($token)
    {
        return $this->findOneBy(array('confirmationToken' => $token));
    }

    /**
     * @see UserRepositoryInterface::findOneByRememberMeToken
     */
    public function findOneByRememberMeToken($token)
    {
        if(empty($token)) {
            return null;
        }

        return $this->findOneBy(array('rememberMeToken' => $token));
    }

    protected function isValidEmail($email)
    {
        return preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $email);
    }
}
