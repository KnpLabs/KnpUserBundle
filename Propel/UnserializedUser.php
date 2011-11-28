<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Propel;


class UnserializedUser
{
    private $username;
    private $usernameCanonical;
    private $salt;
    private $password;
    private $expired;
    private $locked;
    private $credentialsExpired;
    private $enabled;

    public function __construct($username, $usernameCanonical, $salt, $password, $expired, $locked, $credentialsExpired, $enabled)
    {
        $this->username = $username;
        $this->usernameCanonical = $usernameCanonical;
        $this->salt = $salt;
        $this->password = $password;
        $this->expired = $expired;
        $this->locked = $locked;
        $this->credentialsExpired = $credentialsExpired;
        $this->enabled = $enabled;
    }

    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getExpired()
    {
        return $this->expired;
    }

    public function getLocked()
    {
        return $this->locked;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    public function getCredentialsExpireAt()
    {
        return null;
    }

    public function getExpiresAt()
    {
        return null;
    }

    public function __call($method, $arguments)
    {
        throw new \BadMethodCallException();
    }
}
