<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Components\Form\Form;
use Symfony\Components\Form\TextField;
use Symfony\Components\Form\PasswordField;

class UserForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('username'));
        $this->add(new TextField('email'));
        $this->add(new PasswordField('password'));
    } 
}
