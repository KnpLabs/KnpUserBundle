<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MailerInterface
{
    /**
     * Send an email to a user to confirm the account creation
     *
     * @param UserInterface $user
     * @param string $engine the templating engine name, generally 'twig' or 'php'
     */
    function sendConfirmationEmailMessage(UserInterface $user, $engine);

    /**
     * Send an email to a user to confirm the password reset
     *
     * @param UserInterface $user
     * @param string $engine the templating engine name, generally 'twig' or 'php'
     */
    function sendResettingEmailMessage(UserInterface $user, $engine);
}
