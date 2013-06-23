<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Security\Encoder;

use Bundle\FOS\UserBundle\Model\User;

interface EncoderFactoryInterface
{
    /**
     * Returns an instance of PasswordEncoderInterface to use for encoding the
     * password.
     * 
     * @param User $account
     * @return PasswordEncoderInterface
     */
    function getEncoder(User $account);
}