<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Christophe Coevoet <stof@notk.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Bundle\FOS\UserBundle\Model\Group;
use Bundle\FOS\UserBundle\Form\ChangePassword;

/**
 * RESTful controller managing group CRUD
 */
class GroupController extends Controller
{
    /**
     * Show all groups
     */
    public function listAction()
    {
        $groups = $this->get('fos_user.group_manager')->findGroups();

        return $this->render('FOS\UserBundle:Group:list.'.$this->getRenderer(), array('groups' => $groups));
    }

    /**
     * Show one group
     */
    public function showAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        return $this->render('FOS\UserBundle:Group:show.'.$this->getRenderer(), array('group' => $group));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->createForm($group);

        return $this->render('FOS\UserBundle:Group:edit.'.$this->getRenderer(), array(
            'form'      => $form,
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Update a group
     */
    public function updateAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->createForm($group);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $this->get('fos_user.group_manager')->updateGroup($group);
            $this->get('session')->setFlash('fos_user_group_update', 'success');
            $groupUrl = $this->generateUrl('fos_user_group_show', array('groupname' => $group->getName()));
            return $this->redirect($groupUrl);
        }

        return $this->render('FOS\UserBundle:Group:edit.'.$this->getRenderer(), array(
            'form'      => $form,
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->createForm();

        return $this->render('FOS\UserBundle:Group:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Create a group
     */
    public function createAction()
    {
        $form = $this->createForm();
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $group = $form->getData();
            $this->get('fos_user.group_manager')->updateGroup($group);

            $this->get('session')->setFlash('fos_user_group_create', 'success');
            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('groupname' => $group->getName())));
        }

        return $this->render('FOS\UserBundle:Group:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Delete one group
     */
    public function deleteAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $this->get('fos_user.group_manager')->deleteGroup($group);
        $this->get('session')->setFlash('fos_user_group_delete', 'success');

        return $this->redirect($this->generateUrl('fos_user_group_list'));
    }

    /**
     * Find a group by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     * @throws NotFoundException if user does not exist
     * @return Group
     */
    protected function findGroupBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }

    /**
     * Create a GroupForm instance and returns it
     *
     * @param Group $object
     * @return Bundle\FOS\UserBundle\Form\GroupForm
     */
    protected function createForm($object = null)
    {
        $form = $this->get('fos_user.form.group');
        if (null === $object) {
            $object = $this->get('fos_user.group_manager')->createGroup('');
        }

        $form->setData($object);

        return $form;
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('fos_user.template.renderer');
    }
}
