<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Type\AbstractType;
use Symfony\Component\Form\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('plainPassword', 'repeated', array('type' => 'password'))
        ->end();
    }
}
