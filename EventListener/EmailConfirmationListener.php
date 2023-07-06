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

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @internal
 *
 * @final
 */
class EmailConfirmationListener implements EventSubscriberInterface
{
    private $mailer;
    private $tokenGenerator;
    private $router;

    /**
     * EmailConfirmationListener constructor.
     */
    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        ];
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();

        $user->setEnabled(false);
        if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }

        $this->mailer->sendConfirmationEmailMessage($user);

        $event->getRequest()->getSession()->set('fos_user_send_confirmation_email/email', $user->getEmail());

        $url = $this->router->generate('fos_user_registration_check_email');
        $event->setResponse(new RedirectResponse($url));
    }
}
