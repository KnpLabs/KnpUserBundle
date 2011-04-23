<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Type\AbstractType;
use Symfony\Component\Form\FormBuilder;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
        ->end();

        //$this->add(new RepeatedField(new PasswordField('plainPassword')));
    }
}
