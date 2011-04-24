<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;


class ResetPasswordFormType extends AbstractType
{
    public function configure()
    {
        $builder->add('new', 'repeated', array('type' => 'password'))->end();
    }
}
