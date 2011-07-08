<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $child = $builder
            ->create('user', 'form')
            ->add('username')
            ->add('email')
        ;
        $builder
            ->add($child)
            ->add('current', 'password')
        ;
    }

    public function getName()
    {
        return 'fos_user_profile';
    }
}
