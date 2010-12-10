<?php

namespace Bundle\DoctrineUserBundle\Security;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Bundle\DoctrineUserBundle\Model\UserRepositoryInterface;

class TokenUnserializeListener
{
    protected $dispatcher;
    protected $userRepository;

    public function __construct(EventDispatcher $dispatcher, UserRepositoryInterface $userRepository)
    {
        $this->dispatcher = $dispatcher;
        $this->userRepository = $userRepository;
    }

    public function register()
    {
        $this->dispatcher->connect('security.token.unserialize', array($this, 'listen'));
    }

    public function listen(Event $event)
    {
        $token = $event->getSubject();
        if($user = $token->getUser()) {
            $user = $this->userRepository->find($user->getId());
            // Fancy reflection hack to set the new token user
            $reflClass = new \ReflectionClass(get_class($token));
            $reflProp = $reflClass->getProperty('user');
            $reflProp->setAccessible(true);
            $reflProp->setValue($token, $user);
        }
    }
}
