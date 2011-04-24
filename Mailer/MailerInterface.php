<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

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
