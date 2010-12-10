<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Model;

use Symfony\Component\Security\Role\RoleInterface;

use Bundle\DoctrineUserBundle\Util\String;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\User\AdvancedAccountInterface;
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;

/**
 * Storage agnostic user object
 * Has validator annotation, but database mapping must be done in a subclass.
 */
abstract class User implements AdvancedAccountInterface, MutableRoleHolderInterface
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
    protected $enabled;

    /**
     * The algorithm to use for hashing
     * 
     * @var string
     */
    protected $algorithm;
    
    /**
     * The salt to use for hashing
     * 
     * @var string
     */
    protected $salt;
    
    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @validation:Validation({
     *      @validation:NotBlank(message="Please enter a password", groups="Registration"),
     *      @validation:MinLength(limit=2, message="The password is too short", groups="Registration"),
     *      @validation:MaxLength(limit=255, message="The password is too long", groups="Registration")
     * })
     * @var string
     */
    protected $plainPassword;

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
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * @var Collection
     */
    protected $groups;

    /**
     * @var boolean
     */
    protected $locked;
    
    /**
     * @var boolean
     */
    protected $expired;
    
    /**
     * @var DateTime
     */
    protected $expiresAt;
    
    /**
     * @var array
     */
    protected $roles;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->confirmationToken = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
    }

    /**
     * Return the user roles
     * Implements AccountInterface
     *
     * @return array The roles
     **/
    public function getRoles()
    {
        $roles = $this->roles;
        
        // we need to make sure to have at least one role
        $roles[] = RoleHolderInterface::ROLE_DEFAULT;
        
        return $roles;
    }
    
    public function setRoles(array $roles)
    {
          $this->roles = array();
          
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }
    
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === RoleHolderInterface::ROLE_DEFAULT) {
            return;
        }
        
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }
    
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true))
        {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }
    
    /**
     * Never use this to check if this user has access to anything!
     * 
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     * 
     *         $securityContext->vote('ROLE_USER');
     * 
     * @param string $role
     * @return void
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->roles, true);
    }

    /**
     * Removes sensitive data from the user.
     * Implements AccountInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Checks whether the user's account has expired.
     * Implements AdvancedAccountInterface
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired()
    {
        if (true === $this->expired) {
            return false;
        }
        
        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time()) {
            return false;
        }
        
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
        return !$this->locked;
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
        return $this->enabled;
    }
    
    /**
     * @validation:AssertType(type="boolean")
     * 
     * @return Boolean
     */    
    public function isSuperAdmin()
    {
       return $this->hasRole(RoleInterface::ROLE_SUPERADMIN); 
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
        $this->usernameLower = String::strtolower($username);
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
     * Implementation of AccountInterface
     * @return string
     */
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
        $this->email = String::strtolower($email);
    }

    /**
     * Implements AccountInterface
     * Get the encrypted password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Sets the hashed password.
     * 
     * @param string $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param bool $boolean
     */
    public function setEnabled($boolean)
    {
        $this->enabled = $boolean;
    }
    
    /**
     * Sets the super admin status
     * 
     * @param boolean $boolean
     * @return void
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(RoleHolderInterface::ROLE_SUPERADMIN);
        }
        else {
            $this->removeRole(RoleHolderInterface::ROLE_SUPERADMIN);
        }
    }
    
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }
    
    public function getPlainPassword()
    {
        return $this->plainPassword;
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
    
    /**
     * Implementation of AccountInterface.
     * 
     * @param AccountInterface $account
     * @return boolean
     */
    public function equals(AccountInterface $account)
    {
        if (!$account instanceof User) {
            return false;
        }
        
        if ($this->password !== $account->getPassword()) {
            return false;
        }
        if ($this->getSalt() !== $account->getSalt()) {
            return false;
        }
        if ($this->username !== $account->getUsername()) {
            return false;
        }
        if ($this->accountNonExpired !== $account->isAccountNonExpired()) {
            return false;
        }
        if ($this->accountNonLocked !== $account->isAccountNonLocked()) {
            return false;
        }
        if ($this->credentialsNonExpired !== $account->isCredentialsNonExpired()) {
            return false;
        }
        if ($this->enabled !== $account->isEnabled()) {
            return false;
        }
        
        return true;     
    }
}
