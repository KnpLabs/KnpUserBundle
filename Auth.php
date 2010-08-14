<?php

namespace Bundle\DoctrineUserBundle;

use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\DAO\UserRepository;
use Symfony\Components\HttpFoundation\Session;

/**
 * The Auth service binds a User Entity or Document to the Symfony2 Session 
 */
class Auth
{
    /**
     * The generic User repository
     *
     * @var UserRepository
     */
    protected $userRepository = null;

    /**
     * The Symfony2 Session service
     *
     * @var Session
     */
    protected $session = null;

    /**
     * The user object bound to the session, if any
     *
     * @var User
     */
    protected $user = null;

    /**
     * Instanciate the Auth service 
     * 
     * @param UserRepository $userRepository 
     * @param Session $session 
     */
    public function __construct(UserRepository $userRepository, Session $session, array $options = array())
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->options = array_merge(array(
            'session_path' => 'doctrine_user/auth/identifier',
            'user_identifier' => 'id'
        ));
        $this->session->start();
    }

    /**
     * Log in a user, bind it to the session and update its last login date
     *
     * @return null
     **/
    public function login(User $user)
    {
        // bind user identifier to the session
        $this->session->setAttribute($this->options['session_path'], $this->getUserIdentifierValue($user));

        // update user last login date
        $user->setLastLogin(new \DateTime());
        $this->userRepository->getObjectManager()->persist($user);
        $this->userRepository->getObjectManager()->flush();
    }

    /**
     * Log out a user
     *
     * @return null
     **/
    public function logout()
    {
        $this->session->setAttribute($this->options['session_path'], null);
    }

    /**
     * Get the authenticated user
     * @return User
     */
    public function getUser()
    {
        if(null === $this->user && $this->session->getAttribute($this->options['session_path'])) {
            $this->user = $this->userRepository->findOneByIdentifier($this->session->getAttribute($this->options['session_path']));
        }

        return $this->user;
    }

    /**
     * Get the value of the user identifier
     *
     * @return mixed
     **/
    protected function getUserIdentifierValue(User $user)
    {
        $getter = 'get'.ucfirst($this->options['user_identifier']);
        return $user->$getter();
    }

}
