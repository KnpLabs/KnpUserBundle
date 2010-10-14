<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

class ChangePasswordForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('current'));
        $this->add(new RepeatedField(new PasswordField('new')));
    }

    public function getNewPassword()
    {
        return $this->getData()->new;
    }
}
