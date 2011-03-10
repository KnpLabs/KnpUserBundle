<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\RepeatedField;
use Symfony\Component\Form\PasswordField;

use Symfony\Component\Validator\ValidatorInterface;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class UserForm extends Form
{
    protected $request;
    protected $userManager;

    /**
     * Constructor.
     *
     * @param string $title
     * @param array $options
     */
    public function __construct($title, array $options = array())
    {
        $this->addOption('theme');

        parent::__construct($title, $options);
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
        $this->add(new TextField('username'));
        $this->add(new TextField('email'));
        $this->add(new RepeatedField(new PasswordField('plainPassword')));
    }

    public function bind(Request $request, $data = null)
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
        if ('POST' == $request->getMethod()) {
            $values = $request->request->get($this->getName(), array());
            $files = $request->files->get($this->getName(), array());

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
