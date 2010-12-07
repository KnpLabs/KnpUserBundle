<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;
use Symfony\Component\EventDispatcher\Event;

class DoctrineUserBundle extends BaseBundle
{
    public function boot()
    {
        $this->container->get('event_dispatcher')->connect('security.token.unserialize', array($this, 'listenToSecurityTokenUnserialize'));
    }

    public function listenToSecurityTokenUnserialize(Event $event)
    {
        if($user = $event->getSubject()->getUser()) {
            $this->container->get('doctrine_user.object_manager')->merge($user);
        }
    }
}
