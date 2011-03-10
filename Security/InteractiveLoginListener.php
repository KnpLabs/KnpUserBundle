<?php

namespace FOS\UserBundle\Security;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\Event;
use DateTime;

class InteractiveLoginListener
{
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function listenToInteractiveLogin(Event $event)
    {
        $user = $event->get('token')->getUser();

        if ($user instanceof User) {
            $user->setLastLogin(new DateTime());
            $this->userManager->updateUser($user);
        }
    }
}
