<?php

namespace Bundle\DoctrineUserBundle\Security;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class TokenUnserializeListener
{
    protected $dispatcher;
    protected $objectManager;

    public function __construct(EventDispatcher $dispatcher, $objectManager)
    {
        $this->dispatcher = $dispatcher;
        $this->objectManager = $objectManager;
    }

    public function register()
    {
        $this->dispatcher->connect('security.token.unserialize', array($this, 'listen'));
    }

    public function listen(Event $event)
    {
        $token = $event->getSubject();
        if($user = $token->getUser()) {
            $user = $this->objectManager->find(get_class($user), $user->getId());
            // Fancy reflection hack to set the new token user
            $reflClass = new \ReflectionClass(get_class($token));
            $reflProp = $reflClass->getProperty('user');
            $reflProp->setAccessible(true);
            $reflProp->setValue($token, $user);
        }
    }
}
