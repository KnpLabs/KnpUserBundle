<?php

namespace Bundle\DoctrineUserBundle;

use Bundle\DoctrineUserBundle\DAO\User;
use Bundle\DoctrineUserBundle\DAO\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session;

/**
 * The Auth service binds a User Entity or Document to the Symfony2 Session 
 */
class Auth
{
    /**
     * A User repository
     *
     * @var UserRepositoryInterface
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
     * @param UserRepositoryInterface $userRepository 
     * @param Session $session 
     */
    public function __construct(UserRepositoryInterface $userRepository, Session $session, array $options = array())
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->options = array_merge(array(
            'session_path' => 'doctrine_user/auth/identifier'
        ));

        // make sure session is started
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
        $this->session->set($this->options['session_path'], $this->getUserIdentifierValue($user));

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
        $this->session->set($this->options['session_path'], null);
    }

    /**
     * Get the authenticated user
     * @return User
     **/
    public function getUser()
    {
        if(null === $this->user && ($userId = $this->session->get($this->options['session_path']))) {
            $this->user = $this->userRepository->find($userId);
        }

        return $this->user;
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function isAuthenticated()
    {
        return (bool) $this->getUser();
    }

    /**
     * Get the value of the user identifier
     *
     * @return mixed
     **/
    protected function getUserIdentifierValue(User $user)
    {
        $getter = 'get'.ucfirst($this->userRepository->getObjectIdentifier());

        return $user->$getter();
    }
    
    public function getObjectClass() 
    {
    	return $this->options["doctrine_user.auth.class"];    	
    }
    
}
