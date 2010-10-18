<?php

namespace Bundle\DoctrineUserBundle\Form;
use Bundle\DoctrineUserBundle\Model\User;

class ChangePassword
{
    /**
     * User who changes the password
     *
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    public $current;

    /**
     * @validation:Validation({
     *      @validation:NotBlank(),
     *      @validation:MinLength(limit=2),
     *      @validation:MaxLength(limit=255)
     * })
     * @var string
     */
    public $new;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /** @validation:AssertTrue(message="Wrong password") */
    public function getCurrent()
    {
        return $this->user->checkPassword($this->current);
    }

}
