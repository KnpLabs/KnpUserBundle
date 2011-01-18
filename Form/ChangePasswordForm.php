<?php

namespace Bundle\FOS\UserBundle\Form;

use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

class ChangePasswordForm extends ResetPasswordForm
{
    public function configure()
    {
        $this->add(new PasswordField('current'));
        parent::configure();
    }
}
