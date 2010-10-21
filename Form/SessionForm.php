<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\CheckboxField;

class SessionForm extends Form
{
    public $theme = array();

    public function configure()
    {
        $this->add(new TextField('usernameOrEmail'));
        $this->add(new PasswordField('password'));
        $this->add(new CheckboxField('rememberMe'));
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }
}
