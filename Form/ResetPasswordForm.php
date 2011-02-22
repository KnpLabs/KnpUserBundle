<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Form\ResetPassword;

class ResetPasswordForm extends Form
{
    protected $request;
    protected $userManager;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array|object $data
     * @param ValidatorInterface $validator
     * @param array $options
     */
    public function __construct($name, array $options = array())
    {
        $this->addOption('theme');

        parent::__construct($name, $options);
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function configure()
    {
        $this->add(new RepeatedField(new PasswordField('new')));
    }

    public function getNewPassword()
    {
        return $this->getData()->new;
    }

    public function process(UserInterface $user)
    {
        $this->setData(new ResetPassword($user));

        if ('POST' == $this->request->getMethod()) {
            $this->bind($this->request);

            if ($this->isValid()) {
                $user->setPlainPassword($this->getNewPassword());
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $this->userManager->updateUser($user);

                return true;
            }
        }

        return false;
    }
}
