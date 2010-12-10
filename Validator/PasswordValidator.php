<?php

namespace Bundle\DoctrineUserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Bundle\DoctrineUserBundle\Security\Encoder\EncoderFactoryAwareInterface;
use Bundle\DoctrineUserBundle\Security\Encoder\EncoderFactoryInterface;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordValidator extends ConstraintValidator implements EncoderFactoryAwareInterface
{
    protected $encoderFactory;
    
    public function setEncoderFactory(EncoderFactoryInterface $factory)
    {
        $this->encoderFactory = $factory;
    }
    
    public function isValid($object, Constraint $constraint)
    {
        if (!is_object($object)) {
            throw new \RuntimeException('This is a class constraint.');
        }
        
        $raw = $object->{$constraint->passwordProperty};
        $user = null === $contraint->userProperty ? $object : $object->{$constraint->userProperty};
        $encoder = $this->encoderFactory->getEncoder($user);
        
        return $encoder->isPasswordValid($user->getPassword(), $raw, $user->getSalt());
    }
}