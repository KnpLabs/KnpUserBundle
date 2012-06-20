<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    protected $userManager;

    /**
     * Constructor.
     *
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager
     */
    public function __construct($class, UserManagerInterface $userManager)
    {
        $this->class = $class;
        $this->userManager = $userManager;
    }

    /**
     * Returns the user matching the username.
     *
     * This method uses the user manager service to find the user
     * which matches the username or if no one exists throws a
     * security system relevant exception which results in an
     * authentication error.
     *
     * @param string $username
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * Returns a refreshed instance of the user entity.
     *
     * This method checks if the given user is conform with the user interface
     * and than refreshes its instance from the database.
     * This is usefull if there are changes made to the user profile and the
     * session token needs an update.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     *
     * @return \FOS\UserBundle\Model\UserInterface
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Checks if the user provider supports the requested user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->userManager->getClass() === $class;
    }
}