<?php

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Bundle\DoctrineUserBundle\Model\Permission;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing permission CRUD
 */
class PermissionController extends Controller
{
    /**
     * Shows all permissions
     */
    public function listAction()
    {
        $permissions = $this['doctrine_user.permission_repository']->findAll();

        return $this->render('DoctrineUserBundle:Permission:list', array('permissions' => $permissions));
    }

    /**
     * Shows one permission
     */
    public function showAction($name)
    {
        $permission = $this['doctrine_user.permission_repository']->findOneByName($name);

        if (!$permission) {
            throw new NotFoundHttpException(sprintf('The permission "%s" does not exist.', $name));
        }

        return $this->render('DoctrineUserBundle:Permission:show', array('permission' => $permission));
    }

    /**
     * Shows the permission creation form
     */
    public function newAction($name)
    {
        $form = $this['doctrine_user.permission_form'];
        $form->setData(new Permission());

        return $this->render('DoctrineUserBundle:Permission:new', array('form' => $form));
    }

    /**
     * Creates the permission and redirects to the show page or shows the
     * creation form if it contains errors
     */
    public function createAction($name)
    {
        $form = $this['doctrine_user.permission_form'];
        $form->setData(new Permission());
        $form->bind($this['request']->get($form->getName()));

        if ($form->isValid()) {
            $this['Doctrine.ORM.DefaultEntityManager']->persist($form->getData());
            $this['Doctrine.ORM.DefaultEntityManager']->flush();

            $this['session']->setFlash('doctrine_user_permission_create/success', true);

            return $this->redirect($this->generateUrl('doctrine_user_permission_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Permission:new');
    }

    /**
     * Shows the permission edition form
     */
    public function editAction($name)
    {
        $permission = $this['doctrine_user.permission_repository']->findOneByName($name);

        if (!$permission) {
            throw new NotFoundHttpException(sprintf('The permission "%s" does not exist.', $name));
        }

        $form = $this['doctrine_user.permission_form'];
        $form->setData($permission);

        return $this->render('DoctrineUserBundle:Permission:edit');
    }

    /**
     * Updates the permission and redirects to the show page or shows the
     * edition form if it contains errors
     */
    public function updateAction($name)
    {
        $permission = $this['doctrine_user.permission_repository']->findOneByName($name);

        if (!$permission) {
            throw new NotFoundHttpException(sprintf('The permission "%s" does not exist.', $name));
        }

        $form = $this['doctrine_user.permission_form'];
        $form->setData($permission);
        $form->bind($this['request']->get($form->getName()));

        if ($form->isValid()) {
            $this['Doctrine.ORM.DefaultEntityManager']->persist($form->getData());
            $this['Doctrine.ORM.DefaultEntityManager']->flush();

            $this['session']->setFlash('doctrine_user_permission_update/success', true);

            return $this->redirect($this->generateUrl('doctrine_user_permission_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Permission:edit');
    }

    /**
     * Deletes the specified permission and redirects to the permissions list
     */
    public function deleteAction($name)
    {
        $permission = $this['doctrine_user.permission_repository']->findOneByName($name);

        if (!$permission) {
            throw new NotFoundHttpException(sprintf('The permission "%s" does not exist.', $name));
        }

        $this['doctrine_user.permission_repository']->getObjectManager()->delete($permission);
        $this['doctrine_user.permission_repository']->getObjectManager()->flush();

        $this['session']->setFlash('doctrine_user_permission_delete/success');

        return $this->redirect($this->generateUrl('doctrine_user_permission_list'));
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
