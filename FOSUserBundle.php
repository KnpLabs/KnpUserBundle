<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FOSUserBundle extends Bundle
{
    public function boot()
    {
        $this->container->get('fos_user.security.interactive_login_listener')->register($this->container->get('event_dispatcher'));
    }
}
