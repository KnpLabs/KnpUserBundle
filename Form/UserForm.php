<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

class UserForm extends Form
{
    /**
     * Constructor.
     *
     * @param string $title
     * @param array $options
     */
    public function __construct($title, array $options = array())
    {
        $this->addOption('theme');

        parent::__construct($title, $options);
    }

    public function configure()
    {
        $this->add(new TextField('username'));
        $this->add(new TextField('email'));
        $this->add(new RepeatedField(new PasswordField('plainPassword')));
    }
}
