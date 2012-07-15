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

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Propel\om\BaseUser;

class User extends BaseUser implements UserInterface
{
    /**
     * Plain password. Used when changing the password. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->salt,
                $this->password,
                $this->expired,
                $this->locked,
                $this->credentials_expired,
                $this->locked,
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->salt,
            $this->password,
            $this->expired,
            $this->locked,
            $this->credentials_expired,
            $this->locked
        ) = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritDoc}
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonExpired()
    {
        if (true === $this->getExpired()) {
            return false;
        }

        if (null !== $this->getExpiresAt() && $this->getExpiresAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->getLocked();
    }

    /**
     * {@inheritDoc}
     */
    public function isCredentialsNonExpired()
    {
        if (true === $this->getCredentialsExpired()) {
            return false;
        }

        if (null !== $this->getCredentialsExpireAt() && $this->getCredentialsExpireAt()->getTimestamp() < time()) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritDoc}
     */
    public function isUser(UserInterface $user = null)
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setSuperAdmin($boolean)
    {
        if ($boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    // Overwrite all datetime getters to return a DateTime by default.

    /**
     * {@inheritDoc}
     */
    public function getLastLogin($format = null)
    {
        return parent::getLastLogin($format);
    }

    /**
     * {@inheritDoc}
     */
    public function getExpiresAt($format = null)
    {
        return parent::getExpiresAt($format);
    }

    /**
     * {@inheritDoc}
     */
    public function getPasswordRequestedAt($format = null)
    {
        return parent::getPasswordRequestedAt($format);
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentialsExpireAt($format = null)
    {
        return parent::getCredentialsExpireAt($format);
    }

    /**
     * @param \DateTime $v
     *
     * @return User
     *
     * TODO remove it once https://github.com/willdurand/TypehintableBehavior/issues/4 is fixed
     */
    public function setPasswordRequestedAt(\DateTime $v = null)
    {
        return parent::setPasswordRequestedAt($v);
    }
}
