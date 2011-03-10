<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;

use Symfony\Component\Validator\ValidatorInterface;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;

class GroupForm extends Form
{
    protected $request;
    protected $groupManager;

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

    public function setGroupManager(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    public function configure()
    {
        $this->add(new TextField('name'));
    }

    public function process(GroupInterface $group = null)
    {
        if (null === $group) {
            $group = $this->groupManager->createGroup('');
        }

        $this->setData($group);

        if ('POST' == $this->request->getMethod()) {
            $this->bind($this->request);

            if ($this->isValid()) {
                $this->groupManager->updateGroup($group);
                return true;
            }
        }

        return false;
    }
}
