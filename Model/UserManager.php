<?php

namespace Bundle\FOS\UserBundle\Model;

use Bundle\FOS\UserBundle\Util\String;
use Symfony\Component\Security\Exception\UnsupportedAccountException;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Symfony\Component\Security\User\AccountInterface;
use Symfony\Component\Security\User\UserProviderInterface;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class UserManager implements UserManagerInterface, UserProviderInterface
{
    protected $encoder;
    protected $algorithm;

    public function __construct($encoder, $algorithm)
    {
        $this->encoder = $encoder;
        $this->algorithm = $algorithm;
    }

    /**
     * Returns an empty user instance
     *
     * @return User
     */
    public function createUser()
    {
        $class = $this->getClass();

        return new $class;
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     * @return User
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('email' => $email));
    }

    /**
     * Finds a user by username
     *
     * @param string $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(array('username' => $username));
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string $usernameOrEmail
     * @return User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (String::isEmail($usernameOrEmail)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(array('confirmation_token' => $token));
    }

    /**
     * Finds a user by account
     *
     * It is strongly discouraged to use this method manually as it bypasses
     * all ACL checks.
     *
     * @param AccountInterface $user
     * @return User
     */
    public function loadUserByAccount(AccountInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedAccountException('Account is not supported.');
        }

        return $this->loadUserByUsername((string) $user);
    }

    /**
     * Loads a user by username
     *
     * It is strongly discouraged to call this method manually as it bypasses
     * all ACL checks.
     *
     * @RunAs(roles="ROLE_SUPERADMIN")
     * @param string $username
     * @return AccountInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setAlgorithm($this->algorithm);
            $user->setPassword($this->encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
}
