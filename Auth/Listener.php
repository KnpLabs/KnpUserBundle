<?php

namespace Bundle\DoctrineUserBundle\Auth;

use Symfony\Components\HttpFoundation\Session;
use Symfony\Framework\EventDispatcher;
use Symfony\Components\EventDispatcher\Event;
use Bundle\DoctrineUserBundle\DAO\UserRepository;

class Listener
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var UserRepository
     */
    protected $repo;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher = null;

    public function __construct(Session $session, UserRepository $repo, EventDispatcher $eventDispatcher)
    {
        $this->session = $session;
        $this->repo = $repo;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function connect()
    {
        $this->eventDispatcher->connect('doctrine_user.login', array($this, 'listenToUserLoginEvent'));
        $this->eventDispatcher->connect('doctrine_user.logout', array($this, 'listenToUserLogoutEvent'));
    }

    public function listenToUserLoginEvent(Event $event)
    {
        $this->session->setAttribute('identity/user_id', $event['user']->getId());

        try {
            $event['user']->setLastLogin(new \DateTime());
            $this->repo->getObjectManager()->flush();
        }
        catch(\Exception $e) {}
    }

    public function listenToUserLogoutEvent(Event $event)
    {
        $this->session->setAttribute('identity/user_id', null);
    }
}
