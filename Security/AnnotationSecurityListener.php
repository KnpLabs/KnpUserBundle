<?php

namespace Bundle\DoctrineUserBundle\Security;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Bundle\DoctrineUserBundle\Auth;
use Bundle\DoctrineUserBundle\Configuration\Security;
use Bundle\DoctrineUserBundle\Exception\AuthenticationRequiredException;
use Bundle\DoctrineUserBundle\Exception\InsufficientPermissionsException;

class AnnotationSecurityListener
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Registers a core.controller listener.
     *
     * @param EventDispatcher $dispatcher An EventDispatcher instance
     * @param integer         $priority   The priority
     */
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.controller', array($this, 'filter'), $priority);
    }

    /**
     *
     * @param Event $event
     */
    public function filter(Event $event, $controller)
    {
        $request = $event->getParameter('request');

        if (!$configuration = $request->attributes->get('_converters')) {
            return $controller;
        }

        if (!$configuration->getIsSecure()) {
            return $controller;
        }

        if (!$this->auth->isAuthenticated()) {
            throw new AuthenticationRequiredException();
        }

        if (!$this->auth->hasCredentials($configuration->getCredentials())) {
            throw new InsufficientPermissionsException();
        }

        return $controller;
    }

}