<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Type\AbstractType;
use Symfony\Component\Form\Type\RepeatedType;
use Symfony\Component\Form\FormBuilder;

class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name')->end();
    }
}
