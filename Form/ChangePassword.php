<?php

namespace FOS\UserBundle\Form;
use FOS\UserBundle\Model\User;

class ChangePassword extends ResetPassword
{
    /**
     * @var string
     */
    public $current;
}
