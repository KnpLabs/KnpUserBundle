<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\DAO;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @var string
     */
    protected $username;

    /**
     * @Validation({
     *      @Email(),
     *      @NotBlank(),
     *      @MaxLength(limit=255)
     * })
     * @var string
     */
    protected $email;

    /**
     * @Validation({
     *      @AssertType(type="boolean")
     * })
     * @var boolean
     */
    protected $isActive;

    /**
     * @Validation({
     *      @AssertType(type="boolean")
     * })
     * @var boolean
     */
    protected $isSuperAdmin;

    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     * @var string
     */
    protected $password;

    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=31)
     * })
     * @var string
     */
    protected $algorithm;

    /**
     * @Validation({
     *      @NotBlank(),
     *      @MinLength(limit=2),
     *      @MaxLength(limit=255)
     * })
     * @var string
     */
    protected $salt;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email adress in order to verify it
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var Collection
     */
    protected $permissions;

    public function __construct()
    {
        $this->algorithm = 'sha1';
        $this->salt = md5(uniqid() . rand(100000, 999999));
        $this->confirmationToken = md5(uniqid() . rand(100000, 999999));
        $this->isActive = false;
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
     * Get confirmationToken
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmationToken
     * @param  string
     * @return null
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * Get groups granted to the user 
     * 
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * Gets the name of the groups which includes the user
     *
     * @return array
     */
    public function getGroupNames()
    {
        $names = array();
        foreach($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

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
     * Add a group to the user groups
     *
     * @param Group $group
     * @return null
     **/
    public function addGroup(Group $group)
    {
        if(!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
    }

    /**
     * Get permissions granted to the user 
     * 
     * @return Collection
     */
    public function getPermissions()
    {
        return $this->permissions ?: $this->permissions = new ArrayCollection();
    }

    /**
     * Gets the name of the permissions granted to the user
     *
     * @return array
     */
    public function getPermissionNames()
    {
        $names = array();
        foreach($this->getPermissions() as $permission) {
            $names[] = $permission->getName();
        }

        return $names;
    }

    /**
     * Get all permissions, including user groups permissions 
     *
     * @return ArrayCollection
     */
    public function getAllPermissions()
    {
        $permissions = $this->getPermissions()->toArray();

        foreach($this->getGroups() as $group) {
            $permissions = array_merge($permissions, $group->getPermissions()->toArray());
        }

        return new ArrayCollection(array_unique($permissions));
    }

    /**
     * Get all permission names, including user groups permissions 
     *
     * @return array
     */
    public function getAllPermissionNames()
    {
        $names = array();
        foreach($this->getAllPermissions() as $permission) {
            $names[] = $permission->getName();
        }

        return $names;
    }

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

    /**
     * Add a permission to the user permissions
     *
     * @param Permission $permission
     * @return null
     **/
    public function addPermission(Permission $permission)
    {
        if(!$this->getPermissions()->contains($permission)) {
            $this->getPermissions()->add($permission);
        }
    }

    /**
     * Tell if the the given user is this user 
     * Useful when not hydrating all fields.
     * 
     * @param User $user 
     * @return boolean
     */
    public function is(User $user = null)
    {
        return null !== $user && $this->getUsername() === $user->getUsername();
    }
}
