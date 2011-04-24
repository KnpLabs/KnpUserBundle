<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserFormHandler
{
    protected $request;
    protected $userManager;
    protected $form;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager)
    {
        $this->form = $form;
        $this->request = $request;
        $this->userManager = $userManager;
    }

    public function process(UserInterface $user = null, $confirmation = null)
    {
        if (null === $user) {
            $user = $this->userManager->createUser();
        }

        $this->form->setData($user);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);
            $this->userManager->updateCanonicalFields($this->form->getData());

            if ($this->form->isValid()) {
                if (true === $confirmation) {
                    $user->setEnabled(false);
                } else if (false === $confirmation) {
                    $user->setConfirmationToken(null);
                    $user->setEnabled(true);
                }

                $this->userManager->updateUser($user);

                return true;
            }
        }

        return false;
    }
}
