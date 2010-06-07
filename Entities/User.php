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

/**
 * @Entity(repositoryClass="Bundle\DoctrineUserBundle\Entities\UserRepository")
 * @Table(name="sf_doctrine_user")
 * @HasLifecycleCallbacks
 */
class User
{
    /**
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(name="username", type="string", length=255, unique=true)
     */
    protected $username;
    /**
     * @Column(name="is_active", type="boolean", nullable=false)
     */
    protected $isActive;
    /**
     * @Column(name="is_super_admin", type="boolean", nullable=false)
     */
    protected $isSuperAdmin;
    /**
     * @Column(name="password", type="string", length=127, nullable=false)
     */
    protected $password;
    /**
     * @Column(name="algorithm", type="string", length=127)
     */
    protected $algorithm;
    /**
     * @Column(name="salt", type="string", length=127, nullable=false)
     */
    protected $salt;
    /**
     * @Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;
    /**
     * @Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;
    /**
     * @Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    public function __construct()
    {
        $this->algorithm = 'sha1';
        $this->salt = md5(uniqid() . rand(100000, 999999));
        $this->isActive = true;
        $this->isSuperAdmin = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        if (empty($password)) {
            throw new \InvalidArgumentException('Password can not be empty');
        }

        $this->password = $this->encryptPassword($password);
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    public function getIsSuperAdmin()
    {
        return $this->isSuperAdmin;
    }

    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $time)
    {
        $this->lastLogin = $time;
    }

    protected function encryptPassword($password)
    {
        $algorithm = $this->getAlgorithm();
        $salt = $this->getSalt();

        return $algorithm($salt . $password);
    }

    /**
     * Returns whether or not the given password is valid.
     *
     * @param string $password
     * @return boolean
     */
    public function checkPassword($password)
    {
        return $this->password === $this->encryptPassword($password);
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    /** @PrePersist */
    public function incrementCreatedAt() {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    /** @PreUpdate */
    public function incrementUpdatedAt() {
        $this->updatedAt = new \DateTime();
    }

}
