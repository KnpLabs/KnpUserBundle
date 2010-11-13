<?php

namespace Bundle\DoctrineUserBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Bundle\DoctrineUserBundle\Model\User;
use Symfony\Component\Security\SecurityContext;

/**
 * SecurityHelper.
 */
class SecurityHelper extends Helper
{
    protected $securityContext;

    /**
     * Constructor.
     *
     * @param Auth the Auth service instance
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Returns the authenticated user, if any
     *
     * @return Bundle\DoctrineUserBundle\Model\User
     */
    public function getUser()
    {
        return $this->securityContext->getUser();
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function isAuthenticated()
    {
        return $this->securityContext->isAuthenticated();
    }

    /**
     * Tell whether or not the user is anonymous
     *
     * @return bool
     **/
    public function isAnonymous()
    {
        $token = $this->securityContext->getToken();

        if(!$token) {
            return true;
        }

        return !$token->getUser() instanceof User;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'doctrine_user_security';
    }
}
