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
        $class = $this->container->get('fos_user.user_manager')->getClass();
        call_user_func(array($class, 'setCanonicalizer'), $this->container->get('fos_user.util.canonicalizer'));
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}
