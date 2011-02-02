<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Christophe Coevoet <stof@notk.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use FOS\UserBundle\Model\Group;
use FOS\UserBundle\Form\ChangePassword;

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

        return $this->render('FOSUserBundle:Group:list.html.'.$this->getEngine(), array('groups' => $groups));
    }

    /**
     * Show one group
     */
    public function showAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        return $this->render('FOSUserBundle:Group:show.html.'.$this->getEngine(), array('group' => $group));
    }

    /**
     * Edit one group, show the edit form
     */
    public function editAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $form = $this->get('fos_user.form.group');
        $form->setData($group);

        $form = $this->createForm($group);

        return $this->render('FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
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
        $form = $this->get('fos_user.form.group');
        $form->bind($this->get('request'), $group);

        if ($form->isValid()) {
            $this->get('fos_user.group_manager')->updateGroup($group);
            $this->setFlash('fos_user_group_update', 'success');
            $groupUrl = $this->generateUrl('fos_user_group_show', array('groupname' => $group->getName()));
            return $this->redirect($groupUrl);
        }

        return $this->render('FOSUserBundle:Group:edit.html.'.$this->getEngine(), array(
            'form'      => $form,
            'groupname'  => $group->getName()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $user = $this->get('fos_user.group_manager')->createGroup('');
        $form = $this->get('fos_user.form.group');
        $form->setData($user);

        return $this->render('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
            'form' => $form
        ));
    }

    /**
     * Create a group
     */
    public function createAction()
    {
        $group = $this->get('fos_user.group_manager')->createGroup('');
        $form = $this->get('fos_user.form.group');
        $form->bind($this->get('request'), $group);

        if ($form->isValid()) {
            $group = $form->getData();
            $this->get('fos_user.group_manager')->updateGroup($group);

            $this->setFlash('fos_user_group_create', 'success');
            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('groupname' => $group->getName())));
        }

        return $this->render('FOSUserBundle:Group:new.html.'.$this->getEngine(), array(
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
        $this->setFlash('fos_user_group_delete', 'success');

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

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

    protected function setFlash($action, $value)
    {
        $this->get('session')->setFlash($action, $value);
    }
}
