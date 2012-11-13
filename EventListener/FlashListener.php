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
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess',
            FOSUserEvents::PROFILE_EDIT_SUCCESS => 'onProfileEditSuccess',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess'
        );
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        $this->session->getFlashBag()->add('success', $this->trans('profile.flash.updated'));
    }

    public function onChangePasswordSuccess(FormEvent $event)
    {
        $this->session->getFlashBag()->add('success', $this->trans('change_password.flash.updated'));
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $this->session->getFlashBag()->add('success', $this->trans('registration.flash.user_created'));
    }

    public function onResettingResetSuccess(FormEvent $event)
    {
        $this->session->getFlashBag()->add('success', $this->trans('resetting.flash.success'));
    }

    private function trans($message, array $params = array())
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}
