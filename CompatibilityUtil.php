<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class CompatibilityUtil
{
    public static function upgradeEventDispatcher(EventDispatcherInterface $eventDispatcher): EventDispatcherInterface
    {
        // On Symfony 5.0+, the legacy proxy is a no-op and it is deprecated in 5.1+
        // Detecting the parent class of GenericEvent (which changed in 5.0) allows to avoid using the deprecated no-op API.
        if (is_subclass_of(GenericEvent::class, Event::class)) {
            return $eventDispatcher;
        }

        // BC layer for Symfony 4.4 where we need to apply the decorating proxy in case of non-upgraded dispatcher.
        return LegacyEventDispatcherProxy::decorate($eventDispatcher);
    }
}
