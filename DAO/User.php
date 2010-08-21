<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Gordon Franke <info@nevalon.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

/**
 * Storage agnostic user object
 * Has validator annotation, but database mapping must be done in a subclass.
 */
abstract class User
{
    protected $id;
    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     */
    protected $username;
    /**
     * @Validation({
     *      @Email(),
     *      @NotBlank(),
     *      @MaxLength(limit=255)
     * })
     */
    protected $email;
    /**
     * @Validation({
     *      @AssertType(type="boolean")
     * })
     */
    protected $isActive;
    /**
     * @Validation({
     *      @AssertType(type="boolean")
     * })
     */
    protected $isSuperAdmin;
    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     */
    protected $password;
    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=31)
     * })
     */
    protected $algorithm;
    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     */
    protected $salt;
    /**
     * @Validation({
     *      @DateTime()
     * })
     */
    protected $createdAt;
    /**
     * @Validation({
     *      @DateTime()
     * })
     */
    protected $updatedAt;
    /**
     * @Validation({
     *      @DateTime()
     * })
     */
    protected $lastLogin;

    protected $groups;
    protected $permissions;
    protected $allPermissions;

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

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     * @param  string
     * @return null
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Password is encrypted and can not be accessed.
     * Returns empty string for use in form password field.
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        if (empty($password))
        {
            $this->password = null;
        }

        $this->password = $this->encryptPassword($password);
    }

    /**
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function getIsSuperAdmin()
    {
        return $this->isSuperAdmin;
    }

    /**
     * @param bool $isActive
     */
    public function setIsSuperAdmin($isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @param \DateTime $time
     */
    public function setLastLogin(\DateTime $time)
    {
        $this->lastLogin = $time;
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

    public function incrementCreatedAt()
    {
        if(null === $this->createdAt)
        {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string encrypted password
     */
    protected function encryptPassword($password)
    {
        return hash_hmac($this->algorithm, $password, $this->salt);
    }

    /**
     * Gets the name of the groups which includes the user
     *
     * @return array
     */
    abstract public function getGroupNames();
    
    /**
     * Indicates whether the user belongs to the specified group or not
     *
     * @param string $name Name of the group
     * @return boolean
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * Gets the name of the permissions granted to the user
     *
     * @return array
     */
    abstract public function getPermissionNames();

    /**
     * Gets the name of all the permissions granted to the user including group
     * permissions
     *
     * @return array
     */
    abstract public function getAllPermissionNames();

    /**
     * Indicates whether the specified permission is granted to the user or not
     *
     * @param string $name Name of the permission
     * @return boolean
     */
    public function hasPermission($name)
    {
        return in_array($name, $this->getAllPermissionNames());
    }
}
