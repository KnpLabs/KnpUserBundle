<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\CheckboxField;

use Symfony\Component\Validator\ValidatorInterface;

class SessionForm extends Form
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param array|object $data
     * @param ValidatorInterface $validator
     * @param array $options
     */
    public function __construct($name, $data, ValidatorInterface $validator, array $options = array())
    {
        $this->addOption('theme');

        parent::__construct($name, $data, $validator, $options);
    }

    public function configure()
    {
        $this->add(new TextField('usernameOrEmail'));
        $this->add(new PasswordField('password'));
        $this->add(new CheckboxField('rememberMe'));
    }
}
