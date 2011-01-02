<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class FOSUserBundle extends BaseBundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        if (!extension_loaded('mb_string')) {
            require_once __DIR__.'/Util/mbstring.php';
        }
    }
}
