<?php

namespace FOS\UserBundle\Form;

use Symfony\Component\Form\Form;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\GroupManagerInterface;

class GroupFormHandler
{
    protected $request;
    protected $groupManager;
    protected $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setGroupManager(GroupManagerInterface $groupManager)
    {
        $this->groupManager = $groupManager;
    }

    public function process(GroupInterface $group = null)
    {
        if (null === $group) {
            $group = $this->groupManager->createGroup('');
        }

        $this->form->setData($group);

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->groupManager->updateGroup($group);
                return true;
            }
        }

        return false;
    }
}
