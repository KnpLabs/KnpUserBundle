<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Components\Form\Form;
use Symfony\Components\Form\TextField;
use Symfony\Components\Form\PasswordField;

class SessionForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('usernameOrEmail'));
        $this->add(new PasswordField('password'));
    } 
}
