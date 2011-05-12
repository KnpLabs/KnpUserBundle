<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;

class ChangePasswordFormType extends ResetPasswordFormType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('current', 'password');
        parent::buildForm($builder, $options);
    }
}
