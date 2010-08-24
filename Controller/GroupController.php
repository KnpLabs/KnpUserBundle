<?php

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller as Controller;
use Bundle\DoctrineUserBundle\DAO\Group;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing group CRUD
 */
class GroupController extends Controller
{
    /**
     * Shows all groups
     */
    public function listAction()
    {
        $groups = $this['doctrine_user.group_repository']->findAll();

        return $this->render('DoctrineUserBundle:Group:list', array('groups' => $groups));
    }

    /**
     * Shows one group
     */
    public function showAction($name)
    {
        $group = $this['doctrine_user.group_repository']->findOneByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist.', $name));
        }

        return $this->render('DoctrineUserBundle:Group:show', array('group' => $group));
    }

    /**
     * Shows the group creation form
     */
    public function newAction($name)
    {
        $form = $this['doctrine_user.group_form'];
        $form->setData(new Group());

        return $this->render('DoctrineUserBundle:Group:new', array('form' => $form));
    }

    /**
     * Creates the group and redirects to the show page or shows the creation
     * form if it contains errors
     */
    public function createAction($name)
    {
        $form = $this['doctrine_user.group_form'];
        $form->setData(new Group());
        $form->bind($this->getRequest()->get($form->getName()));

        if ($form->isValid()) {
            $this['Doctrine.ORM.DefaultEntityManager']->persist($form->getData());
            $this['Doctrine.ORM.DefaultEntityManager']->flush();

            $this['session']->start();
            $this['session']->setFlash('doctrine_user_group_create/success', true);

            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Group:new');
    }

    /**
     * Shows the group edition form
     */
    public function editAction($name)
    {
        $group = $this['doctrine_user.group_repository']->findOneByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist.', $name));
        }

        $form = $this['doctrine_user.group_form'];
        $form->setData($group);

        return $this->render('DoctrineUserBundle:Group:edit');
    }

    /**
     * Updates the group and redirects to the show page or shows the edition
     * form if it contains errors
     */
    public function updateAction($name)
    {
        $group = $this['doctrine_user.group_repository']->findOneByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist.', $name));
        }

        $form = $this['doctrine_user.group_form'];
        $form->setData($group);
        $form->bind($this->getRequest()->get($form->getName()));

        if ($form->isValid()) {
            $this['Doctrine.ORM.DefaultEntityManager']->persist($form->getData());
            $this['Doctrine.ORM.DefaultEntityManager']->flush();

            $this['session']->start();
            $this['session']->setFlash('doctrine_user_group_update/success', true);

            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Group:edit');
    }

    /**
     * Deletes the specified group and redirects to the groups list
     */
    public function deleteAction($name)
    {
        $group = $this['doctrine_user.group_repository']->findOneByName($name);

        if (!$group) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist.', $name));
        }

        $this['doctrine_user.group_repository']->getObjectManager()->delete($group);
        $this['doctrine_user.group_repository']->getObjectManager()->flush();

        $this['session']->start();
        $this['session']->setFlash('doctrine_user_group_delete/success');

        return $this->redirect($this->generateUrl('doctrine_user_group_list'));
    }
}
