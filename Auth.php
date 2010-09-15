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
     * The Symfony2 Request service
     *
     * @var Request
     */
    protected $request = null;

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
     * @param Request $request 
     */
    public function __construct(UserRepositoryInterface $userRepository, Request $request, array $options = array())
    {
        $this->userRepository = $userRepository;
        $this->request = $request;
        $this->session = $request->getSession();
        $this->options = array_merge(array(
            'session_path' => 'doctrine_user/auth/identifier',
            'remember_me_cookie_name' => 'doctrine_user/remember_me'
        ));
    }

    /**
     * Log in a user, bind it to the session and update its last login date
     *
     * @param User $user the user to log in
     * @param boolean $remember whether or not to remember the user
     * @return null
     **/
    public function login(User $user, $remember = false)
    {
        // bind user identifier to the session
        $this->session->set($this->options['session_path'], $this->getUserIdentifierValue($user));

        if($remember) {
            // renew user remember_me token
            $user->renewRememberMeToken();
            // make token a cookie
            $this->request->cookies->set($this->options['remember_me_cookie_name'], $user->getRememberMeToken());
        }

        // update user last login date
        $user->setLastLogin(new \DateTime());
        // save the updated user
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
        $this->session->delete($this->options['session_path']);
        $this->request->cookies->delete($this->options['remember_me_cookie_name']);
    }

    /**
     * Get the authenticated user
     * 
     * @return User
     **/
    public function getUser()
    {
        if (null === $this->user && ($userId = $this->session->get($this->options['session_path']))) {
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
     * Indicates whether the user has credentials
     *
     * @param mixed $crendentials
     * @param bool $useAnd
     * 
     * @return bool
     **/
    public function hasCredentials($credentials, $useAnd = true)
    {
        if (!$user->isAuthenticated()) {
            return false;
        }

        if (empty($credentials)) {
            return true;
        }

        if (!is_array($credentials)) {
            $credentials = array($credentials);
        }

        $test = false;

        foreach ($credentials as $credential) {
            $test = $this->hasCredential($credential, $useAnd ? false : true);

            if ($useAnd) {
                $test = $test ? false : true;
            }

            if ($test) {
                break;
            }
        }

        if ($useAnd) {
            $test = $test ? false : true;
        }

        return $test;
    }

    /**
     * Get the value of the user identifier
     *
     * @return mixed
     **/
    protected function getUserIdentifierValue(User $user)
    {
        $getter = 'get' . ucfirst($this->userRepository->getObjectIdentifier());

        return $user->$getter();
    }
}
