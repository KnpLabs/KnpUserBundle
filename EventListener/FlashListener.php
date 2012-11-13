<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashListener implements EventSubscriberInterface
{
    private static $successMessages = array(
        FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'change_password.flash.updated',
        FOSUserEvents::PROFILE_EDIT_SUCCESS => 'profile.flash.updated',
        FOSUserEvents::REGISTRATION_SUCCESS => 'registration.flash.user_created',
        FOSUserEvents::RESETTING_RESET_SUCCESS => 'resetting.flash.success'
    );

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;
    private $translator;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'addSuccessFlash',
            FOSUserEvents::REGISTRATION_SUCCESS => 'addSuccessFlash',
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'addSuccessFlash'
        );
    }

    public function addSuccessFlash(FormEvent $event)
    {
        if (!isset(self::$successMessages[$event->getName()])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$event->getName()]));
    }

    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}
