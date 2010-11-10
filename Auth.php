<?php

namespace Bundle\DoctrineUserBundle;

use Bundle\DoctrineUserBundle\Model\User;
use Bundle\DoctrineUserBundle\Model\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;

/**
 * The Auth service binds a User Entity or Document to the Symfony2 Session
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
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
     * Array of options
     *
     * @var array
     */
    protected $options = array(
        'session_path' => 'doctrine_user/auth/identifier',
        'remember_me_cookie_name' => 'doctrine_user/remember_me',
        'remember_me_lifetime' => 2592000
    );

    /**
     * Instanciate the Auth service
     *
     * @param UserRepositoryInterface $userRepository The user repository
     * @param Request                 $request        The request service
     * @param array                   $options        An array of options
     */
    public function __construct(UserRepositoryInterface $userRepository, Session $session, Request $request = null, array $options = array())
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->request = $request;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Log in a user, bind it to the session and update its last login date
     *
     * @param User $user the user to log in
     * @return null
     */
    public function login(User $user)
    {
        // log out any precedent user
        $this->logout();

        // bind user identifier to the session
        $this->session->set($this->options['session_path'], $this->getUserIdentifierValue($user));

        // renew user remember_me token
        $user->renewRememberMeToken();
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
     */
    public function logout()
    {
        $this->session->remove($this->options['session_path']);
        $this->user = null;
    }

    /**
     * Get the authenticated user
     *
     * @return User
     */
    public function getUser()
    {
        if (null === $this->user) {
            // if we have a user id in the session
            if($userId = $this->session->get($this->options['session_path'])) {
                $this->user = $this->userRepository->find($userId);
            }
            // if we have a request
            elseif($this->request) {
                // if we have a remember_me token in cookies
                if($userRememberMeToken = $this->request->cookies->get($this->options['remember_me_cookie_name'])) {
                    // if the user exists, login it with the remember parameter to true
                    if($user = $this->userRepository->findOneByRememberMeToken($userRememberMeToken)) {
                        $this->login($user);
                        $this->user = $user;
                    }
                }
            }
        }

        return $this->user;
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return (bool) $this->getUser();
    }

    /**
     * Get an option value
     *
     * @return mixed
     **/
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * Indicates whether the user has credentials
     *
     * @param mixed $credentials A list of credentials
     * @param bool  $useAnd      False will use OR as logical operator
     *
     * @return bool
     */
    public function hasCredentials($credentials, $useAnd = true)
    {
        if (!$this->isAuthenticated()) {
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
            $test = $this->getUser()->hasPermission($credential);

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
     * @param User $user User object
     * @return mixed
     */
    protected function getUserIdentifierValue(User $user)
    {
        $getter = 'get' . ucfirst($this->userRepository->getObjectIdentifier());

        return $user->$getter();
    }
}
