<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;

class UserForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('username'));
        $this->add(new TextField('email'));
        $this->add(new PasswordField('password'));
    } 
}
