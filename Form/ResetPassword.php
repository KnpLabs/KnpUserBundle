<?php

namespace FOS\UserBundle\Form;

use FOS\UserBundle\Model\UserInterface;

class ResetPassword
{
    /**
     * User whose password is changed
     *
     * @var UserInterface
     */
    public $user;

    /**
     * @var string
     */
    public $new;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }
}
