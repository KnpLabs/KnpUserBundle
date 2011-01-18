<?php

namespace Bundle\FOS\UserBundle\Form;
use Bundle\FOS\UserBundle\Model\User;

class ChangePassword extends ResetPassword
{
    /**
     * @var string
     */
    public $current;
}
