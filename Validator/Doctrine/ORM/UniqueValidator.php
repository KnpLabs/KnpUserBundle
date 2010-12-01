<?php

namespace Bundle\DoctrineUserBundle\Validator\Doctrine\ORM;

use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
    public function __construct(EntityManager $dm)
    {
        // TODO Implement the orm unique validator
        throw new \Exception('Not implemented yet');
    }
}
