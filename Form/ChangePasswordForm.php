<?php

namespace Bundle\FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

class ChangePasswordForm extends Form
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
        $this->add(new TextField('current'));
        $this->add(new RepeatedField(new PasswordField('new')));
    }

    public function getNewPassword()
    {
        return $this->getData()->new;
    }
}
