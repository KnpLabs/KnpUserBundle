<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;

class SessionForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('usernameOrEmail'));
        $this->add(new PasswordField('password'));
    } 
}
