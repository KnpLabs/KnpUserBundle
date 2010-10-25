<?php

namespace Bundle\DoctrineUserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;

use Symfony\Component\Validator\ValidatorInterface;

class PermissionForm extends Form
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
        $this->add(new TextField('name'));
        $this->add(new TextareaField('description'));
    }
}
