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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 * @final
 */
class FlashListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private static $successMessages = [
        FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        FOSUserEvents::PROFILE_EDIT_COMPLETED => 'profile.flash.updated',
        FOSUserEvents::REGISTRATION_COMPLETED => 'registration.flash.user_created',
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    ];

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * FlashListener constructor.
     */
    public function __construct(RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::REGISTRATION_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        ];
    }

    /**
     * @param string $eventName
     */
    public function addSuccessFlash(Event $event, $eventName)
    {
        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->getSession()->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    private function getSession(): Session
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \LogicException('Cannot get the session without an active request.');
        }

        return $request->getSession();
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function trans($message, array $params = [])
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}
