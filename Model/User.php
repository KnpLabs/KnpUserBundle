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
use Symfony\Component\Security\Encoder\MessageDigestPasswordEncoder;

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
     * Random string sent to the user email address in order to verify it
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

    public function __construct($algorithm)
    {
        $this->algorithm = $algorithm;
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
        return array('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Tell whether or not the user has a role
     *
     * @return bool
     **/
    public function hasRole($role)
    {
        return in_array($role, $this->getRoles());
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
     * @param string $algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Return the algorithm used to hash the password
     *
     * @return string the algorithm
     **/
    public function getAlgorithm()
    {
        return $this->algorithm;
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
     * Return the salt used to hash the password
     *
     * @return string The salt
     **/
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Sets the plain password. Also encrypts it and fills the encrypted attribute.
     *
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->hashUserPassword();
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

    protected function hashUserPassword()
    {
        if (empty($this->plainPassword)) {
            $this->password = null;
        } else {
            $encoder = new MessageDigestPasswordEncoder($this->getAlgorithm());
            $this->password = $encoder->encodePassword($this->plainPassword, $this->getSalt());
        }
    }
}
