<?php

namespace FOS\UserBundle\Form;
use FOS\UserBundle\Model\User;

class ResetPassword
{
    /**
     * User whose password is changed
     *
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $new;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
