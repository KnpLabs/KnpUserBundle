<?php

namespace Bundle\DoctrineUserBundle\Security\Encoder;

use Bundle\DoctrineUserBundle\Model\User;

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