<?php

namespace Bundle\DoctrineUserBundle\Auth;

use Symfony\Components\HttpFoundation\Session;
use Symfony\Framework\EventDispatcher;
use Symfony\Components\EventDispatcher\Event;
use Doctrine\ORM\EntityManager;

class Listener
{
    /**
     * @var Session
     */
    protected $session;
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher = null;

    public function __construct(Session $session, EntityManager $em, EventDispatcher $eventDispatcher)
    {
        $this->session = $session;
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function connect()
    {
        $this->eventDispatcher->connect('doctrine_user.login', array($this, 'listenToUserLoginEvent'));
        $this->eventDispatcher->connect('doctrine_user.logout', array($this, 'listenToUserLogoutEvent'));
    }

    public function listenToUserLoginEvent(Event $event)
    {
        $this->session->setAttribute('identity', $event['user']);

        $event['user']->setLastLogin(new \DateTime());
        $this->em->flush();
    }

    public function listenToUserLogoutEvent(Event $event)
    {
        $this->session->setAttribute('identity', null);
    }
}
