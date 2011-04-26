<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChangePasswordFormType extends abstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('current', 'password');
    }
}
