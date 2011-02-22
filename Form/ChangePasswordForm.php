<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Form\ChangePassword;

class ChangePasswordForm extends ResetPasswordForm
{
    public function configure()
    {
        $this->add(new PasswordField('current'));
        parent::configure();
    }

    public function process(UserInterface $user)
    {
        $this->setData(new ChangePassword($user));

        if ('POST' == $this->request->getMethod()) {
            $this->bind($this->request);

            if ($this->isValid()) {
                $user->setPlainPassword($this->getNewPassword());
                $this->userManager->updateUser($user);

                return true;
            }
        }

        return false;
    }
}
