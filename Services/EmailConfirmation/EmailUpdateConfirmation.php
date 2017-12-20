<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Services\EmailConfirmation;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Services\EmailConfirmation\Interfaces\EmailEncryptionInterface;
use FOS\UserBundle\Services\EmailConfirmation\Interfaces\EmailUpdateConfirmationInterface;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class EmailUpdateConfirmation.
 */
class EmailUpdateConfirmation implements EmailUpdateConfirmationInterface
{
    const EMAIL_CONFIRMED = 'email_confirmed';

    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var Router
     */
    private $router;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var EmailEncryptionInterface
     */
    private $emailEncryption;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string Email to be confirmed
     */
    private $email;

    /**
     * @var string Route for confirmation link
     */
    private $confirmationRoute;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * EmailUpdateConfirmation constructor.
     *
     * @param Router                   $router
     * @param TokenGenerator           $tokenGenerator
     * @param MailerInterface          $mailer
     * @param EmailEncryptionInterface $emailEncryption
     */
    public function __construct(
        Router $router,
        TokenGenerator $tokenGenerator,
        MailerInterface $mailer,
        EmailEncryptionInterface $emailEncryption,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->router = $router;

        $this->tokenGenerator = $tokenGenerator;

        $this->mailer = $mailer;

        $this->emailEncryption = $emailEncryption;

        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Get $mailer.
     *
     * @return MailerInterface
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Generate new confirmation link for new email based on user confirmation
     * token and hashed new user email.
     *
     * @param Request $request
     *
     * @return string
     */
    public function generateConfirmationLink(Request $request)
    {
        $this->emailEncryption->setUserConfirmationToken(
            $this->getUserConfirmationToken()
        );

        $encryptedEmail = $this->emailEncryption->encryptEmailValue();

        $confirmationParams = array('token' => $this->user->getConfirmationToken(), 'target' => $encryptedEmail);

        $event = new UserEvent($this->user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::EMAIL_UPDATE_INITIALIZE, $event);

        return $this->router->generate(
            $this->confirmationRoute,
            $confirmationParams,
            RouterInterface::ABSOLUTE_URL
        );
    }

    /**
     * Fetch email value from hashed part of confirmation link.
     *
     * @param string $hashedEmail
     *
     * @return string Encrypted email
     */
    public function fetchEncryptedEmailFromConfirmationLink($hashedEmail)
    {
        //replace spaces with plus sign from hash, which could be replaced in url
        $hashedEmail = str_replace(' ', '+', $hashedEmail);

        $this->emailEncryption->setUserConfirmationToken(
            $this->getUserConfirmationToken()
        );

        $email = $this->emailEncryption->decryptEmailValue($hashedEmail);

        return $email;
    }

    /**
     * Set user class instance.
     *
     * @param UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set new user email to be confirmed. Email value should be already
     * validated.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        $this->emailEncryption->setEmail($this->email);

        return $this;
    }

    /**
     * Set route to be used for confirmation ling generation. This route should
     * contain path to confirmation action.
     *
     * @param string $confirmationRoute
     *
     * @return $this
     */
    public function setConfirmationRoute($confirmationRoute)
    {
        $this->confirmationRoute = $confirmationRoute;

        return $this;
    }

    /**
     * Get token which indicates that email was confirmed.
     *
     * @return string
     */
    public function getEmailConfirmedToken()
    {
        return base64_encode(self::EMAIL_CONFIRMED);
    }

    /**
     * Get or create new user confirmation token.
     *
     * @return string
     */
    protected function getUserConfirmationToken()
    {
        // Generate new token if it's not set
        if (!$this->user->getConfirmationToken()) {
            $this->user->setConfirmationToken(
                $this->tokenGenerator->generateToken()
            );
        }

        return $this->user->getConfirmationToken();
    }
}
