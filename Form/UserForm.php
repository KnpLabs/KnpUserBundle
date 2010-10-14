<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

class UserForm extends Form
{
    public function __construct($title, $data, $validator, array $options = array())
    {
        $this->addOption('categoryRepository');
        $this->addOption('columnRepository');
        $this->addOption('showPublishedAt');

        parent::__construct($title, $data, $validator, $options);
    }

    public function configure()
    {
        $this->add(new TextField('username'));
        $this->add(new TextField('email'));
        $this->add(new RepeatedField(new PasswordField('password')));
    }
}
