<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Util;

use FOS\UserBundle\Model\UserInterface;

class Email extends ContainerAware
{
    public function sendConfirmationEmailMessage(UserInterface $user, $engine)
    {
        $template = $this->container->getParameter('fos_user.email.confirmation.template');
        $rendered = $this->renderView($template.'.txt.'.$engine, array(
            'user' => $user,
            'confirmationUrl' =>  $this->container->get('router')->generate('fos_user_user_confirm', array('token' => $user->getConfirmationToken()), true)
        ));
        $this->sendEmailMessage($rendered, $this->getSenderEmail('confirmation'), $user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user, $engine)
    {
        $template = $this->container->getParameter('fos_user.email.resetting_password.template');
        $rendered = $this->renderView($template.'.txt.'.$engine, array(
            'user' => $user,
            'confirmationUrl' =>  $this->container->get('router')->generate('fos_user_user_reset_password', array('token' => $user->getConfirmationToken()), true)
        ));
        $this->sendEmailMessage($rendered, $this->getSenderEmail('resetting_password'), $user->getEmail());
    }

    public function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $mailer = $this->container->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        $mailer->send($message);
    }
}
