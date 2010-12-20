<?php

namespace Bundle\FOS\UserBundle\Form;
use Bundle\FOS\UserBundle\Model\User;

/**
 * @validation:Password(passwordProperty="current", userProperty="user")
 */
class ChangePassword
{
    /**
     * User whose password is changed
     *
     * @var User
     */
    public $user;

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
}
