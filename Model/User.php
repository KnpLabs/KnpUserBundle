<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\User\AdvancedAccountInterface;

/**
 * Storage agnostic user object
 * Has validator annotation, but database mapping must be done in a subclass.
 */
abstract class User implements AdvancedAccountInterface
{
    protected $id;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(message="Please enter a username", groups="Registration"),
     *      @validation:MinLength(limit=2, message="The username is too short", groups="Registration"),
     *      @validation:MaxLength(limit=255, message="The username is too long", groups="Registration")
     * })
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameLower;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(message="Please enter an email", groups="Registration"),
     *      @validation:Email(message="This is not a valid email", groups="Registration"),
     *      @validation:MaxLength(limit=255, message="The email is too long", groups="Registration")
     * })
     * @var string
     */
    protected $email;

    /**
     * @validation:AssertType(type="boolean")
     * @var boolean
     */
    protected $isActive;

    /**
     * @validation:AssertType(type="boolean")
     * @var boolean
     */
    protected $isSuperAdmin;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(message="Please enter a password", groups="Registration"),
     *      @validation:MinLength(limit=2, message="The password is too short", groups="Registration"),
     *      @validation:MaxLength(limit=255, message="The password is too long", groups="Registration")
     * })
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $passwordHash;

    /**
     * @var string
     */
    protected $algorithm;

    /**
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
     * Random string stored in client cookie to enable automatic login
     *
     * @var string
     */
    protected $rememberMeToken;

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
        $this->confirmationToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->renewRememberMeToken();
        $this->isActive = false;
        $this->isSuperAdmin = false;
    }

    /**
     * Return the user roles
     * Implements AccountInterface
     *
     * @return array The roles
     **/
    public function getRoles()
    {
        return array();
    }

    /**
     * Removes sensitive data from the user.
     * Implements AccountInterface
     */
    public function eraseCredentials()
    {
    }

    /**
     * Checks whether the user's account has expired.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->getIsActive();
    }

    /**
     * Return the user unique id
     *
     * @return mixed
     */
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
        $this->usernameLower = static::strtolower($username);
    }

    /**
     * Get the username in lowercase used in search and sort queries
     *
     * @return string
     **/
    public function getUsernameLower()
    {
        return $this->usernameLower;
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
        $this->email = static::strtolower($email);
    }

    /**
     * Hashed password
     * @return string
     */
    public function getPassword()
    {
        return $this->passwordHash;
    }

    /**
     * Return the salt used to hash the password
     *
     * @return string The salt
     **/
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        if (empty($password)) {
            $this->password = null;
        }

        $this->password = $password;
        $this->passwordHash = $this->hashPassword($password);
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
        return $this->passwordHash === $this->hashPassword($password);
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string hashed password
     */
    protected function hashPassword($password)
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
     * Get rememberMeToken
     * @return string
     */
    public function getRememberMeToken()
    {
        return $this->rememberMeToken;
    }

    /**
     * Renew the rememberMeToken
     * @return null
     */
    public function renewRememberMeToken()
    {
        $this->rememberMeToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
        foreach ($this->getGroups() as $group) {
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
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }
    }

    /**
     * Remove a group from the user groups
     *
     * @param Group $group
     * @return null
     **/
    public function removeGroup(Group $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->remove($group);
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
        foreach ($this->getPermissions() as $permission) {
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

        foreach ($this->getGroups() as $group) {
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
        foreach ($this->getAllPermissions() as $permission) {
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
        if (!$this->getPermissions()->contains($permission)) {
            $this->getPermissions()->add($permission);
        }
    }

    /**
     * Remove a permission from the user permissions
     *
     * @param Permission $permission
     * @return null
     **/
    public function removePermission(Permission $permission)
    {
        if ($this->getPermissions()->contains($permission)) {
            $this->getPermissions()->remove($permission);
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

    public static function strtolower($string)
    {
        return extension_loaded('mbstring') ? mb_strtolower($string) : strtolower($string);
    }
}
