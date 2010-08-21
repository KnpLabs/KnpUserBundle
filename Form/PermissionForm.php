<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Components\Form\Form;
use Symfony\Components\Form\TextField;
use Symfony\Components\Form\TextareaField;

class PermissionForm extends Form
{
    public function configure()
    {
        $this->add(new TextField('name'));
        $this->add(new TextareaField('description'));
    }
}
