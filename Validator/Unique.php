<?php

namespace FOS\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Unique extends Constraint
{
    public $message = 'The value for "%property%" already exists.';
    public $property;

    public function defaultOption()
    {
        return 'property';
    }

    public function requiredOptions()
    {
        return array('property');
    }

    public function validatedBy()
    {
        return 'fos_user.validator.unique';
    }

    /**
     * @see Symfony\Component\Validator.Constraint::targets()
     */
    public function targets()
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
