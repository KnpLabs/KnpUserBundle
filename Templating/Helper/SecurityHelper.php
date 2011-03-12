<?php

namespace FOS\UserBundle\Templating\Helper;

use Symfony\Bundle\SecurityBundle\Templating\Helper\SecurityHelper as BaseSecurityHelper;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * SecurityHelper.
 */
class SecurityHelper extends BaseSecurityHelper
{
    /**
     * Returns the authenticated user, if any
     *
     * @return FOS\UserBundle\Model\User
     */
    public function getUser()
    {
        return $this->context->getToken()->getUser();
    }

    /**
     * Tells whether the authenticated user is this user
     *
     * @param UserInterface $user
     * @return bool
     **/
    public function isUser(UserInterface $user)
    {
        $authenticatedUser = $this->getUser();

        return $authenticatedUser instanceof UserInterface && $authenticatedUser->isUser($user);
    }

    /**
     * Tell whether or not a user is logged in
     *
     * @return bool
     **/
    public function isAuthenticated()
    {
        return null !== $this->context->getToken();
    }

    /**
     * Tell whether or not the user is anonymous
     *
     * @return bool
     **/
    public function isAnonymous()
    {
        return false === $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'fos_user_security';
    }
}
