<?php

namespace Bundle\FOS\UserBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Bundle\FOS\UserBundle\Model\UserManager;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * UniqueValidator
 */
class UniqueValidator extends ConstraintValidator
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * Contructor
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Sets the user manager
     *
     * @param UserManager $userManager
     */
    public function setUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Gets the user manager
     *
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * Indicates whether the constraint is valid
     *
     * @param Entity     $value
     * @param Constraint $constraint
     */
    public function isValid($value, Constraint $constraint)
    {
        if (!$this->getUserManager()->validateUnique($value, $constraint)){
            $this->setMessage($constraint->message, array(
                'property' => $constraint->property
            ));
            return false;
        }

        return true;
    }
}
