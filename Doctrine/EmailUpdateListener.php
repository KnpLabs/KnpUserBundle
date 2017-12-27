<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Doctrine;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Services\EmailConfirmation\EmailUpdateConfirmation;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class EmailUpdateListener.
 */
class EmailUpdateListener
{
    /**
     * @var EmailUpdateConfirmation
     */
    private $emailUpdateConfirmation;

    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var CanonicalFieldsUpdater
     */
    private $canonicalFieldsUpdater;

    /**
     * Constructor.
     *
     * @param EmailUpdateConfirmation $emailUpdateConfirmation
     * @param RequestStack            $requestStack
     * @param CanonicalFieldsUpdater  $canonicalFieldsUpdater
     */
    public function __construct(EmailUpdateConfirmation $emailUpdateConfirmation, RequestStack $requestStack, CanonicalFieldsUpdater $canonicalFieldsUpdater)
    {
        $this->emailUpdateConfirmation = $emailUpdateConfirmation;
        $this->requestStack = $requestStack;
        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
    }

    /**
     * Pre update listener based on doctrine common.
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();

        if ($object instanceof UserInterface && $args instanceof PreUpdateEventArgs) {
            $user = $object;

            if ($user->getConfirmationToken() != $this->emailUpdateConfirmation->getEmailConfirmedToken() && isset($args->getEntityChangeSet()['email'])) {
                $oldEmail = $args->getEntityChangeSet()['email'][0];
                $newEmail = $args->getEntityChangeSet()['email'][1];
                $user->setEmail($oldEmail);
                $user->setEmailCanonical($this->canonicalFieldsUpdater->canonicalizeEmail($oldEmail));

                // Configure email confirmation
                $this->emailUpdateConfirmation->setUser($user);
                $this->emailUpdateConfirmation->setEmail($newEmail);
                $this->emailUpdateConfirmation->setConfirmationRoute('fos_user_update_email_confirm');
                $this->emailUpdateConfirmation->getMailer()->sendUpdateEmailConfirmation(
                    $user,
                    $this->emailUpdateConfirmation->generateConfirmationLink($this->requestStack->getCurrentRequest()),
                    $newEmail
                );
            }

            if ($user->getConfirmationToken() == $this->emailUpdateConfirmation->getEmailConfirmedToken()) {
                $user->setConfirmationToken(null);
            }
        }
    }
}
