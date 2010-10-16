<?php

namespace Bundle\DoctrineUserBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Bundle\DoctrineUserBundle\Auth;

/**
 * AuthHelper.
 */
class AuthHelper extends Helper
{
    protected $auth;

    /**
     * Constructor.
     *
     * @param Auth the Auth service instance
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Returns the authenticated user, if any
     *
     * @return Bundle\DoctrineUserBundle\Model\User
     */
    public function getUser()
    {
        return $this->auth->getUser();
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function isAuthenticated()
    {
        return $this->auth->isAuthenticated();
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function getIsAuthenticated()
    {
        return $this->isAuthenticated();
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'auth';
    }
}
