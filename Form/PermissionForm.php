<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;

class PermissionForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('name'));
        $this->add(new TextareaField('description'));
    }
}
