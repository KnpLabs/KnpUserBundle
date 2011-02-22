<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\UserInterface;

interface MailerInterface
{
    function sendConfirmationEmailMessage(UserInterface $user, $engine);

    function sendResettingEmailMessage(UserInterface $user, $engine);
}
