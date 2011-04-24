<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;

/**
 * This mailer does nothing.
 * It is used when the 'email' configuration is not set,
 * and allows to use this bundle without swiftmailer.
 */
class NoopMailer implements MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user, $engine)
    {
        // nothing happens.
    }

    public function sendResettingEmailMessage(UserInterface $user, $engine)
    {
        // nothing happens.
    }
}
