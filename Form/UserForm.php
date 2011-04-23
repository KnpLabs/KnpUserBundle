<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserForm extends Form
{
    protected $request;
    protected $userManager;

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function bind($data = null)
    {
        if (!$this->getName()) {
            throw new FormException('You cannot bind anonymous forms. Please give this form a name');
        }

        // Store object from which to read the default values and where to
        // write the submitted values
        if (null !== $data) {
            $this->setData($data);
        }

        // Store the submitted data in case of a post request
        if ('POST' == $this->request->getMethod()) {
            $values = $this->request->request->get($this->getName(), array());
            $files = $this->request->files->get($this->getName(), array());

            $this->submit(self::deepArrayUnion($values, $files));

            $this->userManager->updateCanonicalFields($this->getData());

            $this->validate();
        }
    }

    public function process(UserInterface $user = null, $confirmation = null)
    {
        if (null === $user) {
            $user = $this->userManager->createUser();
        }

        $this->setData($user);

        if ('POST' == $this->request->getMethod()) {
            $this->bind($this->request);

            if ($this->isValid()) {
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
