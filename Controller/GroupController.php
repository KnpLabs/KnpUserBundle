<?php

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Bundle\DoctrineUserBundle\Model\Group;
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
        $groups = $this->get('doctrine_user.repository.group')->findAll();

        return $this->render('DoctrineUserBundle:Group:list.'.$this->getRenderer(), array('groups' => $groups));
    }

    /**
     * Shows one group
     */
    public function showAction($name)
    {
        $group = $this->findGroup($name);

        return $this->render('DoctrineUserBundle:Group:show.'.$this->getRenderer(), array('group' => $group));
    }

    /**
     * Shows the group creation form
     */
    public function newAction($name)
    {
        $form = $this->createForm();

        return $this->render('DoctrineUserBundle:Group:new.'.$this->getRenderer(), array('form' => $form));
    }

    /**
     * Creates the group and redirects to the show page or shows the creation
     * form if it contains errors
     */
    public function createAction($name)
    {
        $form = $this->createForm();
        $form->bind($this->get('request')->get($form->getName()));

        if ($form->isValid()) {
            $this->get('Doctrine.ORM.DefaultEntityManager')->persist($form->getData());
            $this->get('Doctrine.ORM.DefaultEntityManager')->flush();

            $this->get('session')->setFlash('doctrine_user_group_create', 'success');

            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Group:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Shows the group edition form
     */
    public function editAction($name)
    {
        $group = $this->findGroup($name);
        $form = $this->createForm($group);

        return $this->render('DoctrineUserBundle:Group:edit.'.$this->getRenderer(), array(
            'form'      => $form,
            'name'  => $name
        ));
    }

    /**
     * Updates the group and redirects to the show page or shows the edition
     * form if it contains errors
     */
    public function updateAction($name)
    {
        $group = $this->findGroup($name);
        $form = $this->createForm($group);
        $form->bind($this->get('request')->get($form->getName()));

        if ($form->isValid()) {
            $this->get('Doctrine.ORM.DefaultEntityManager')->persist($form->getData());
            $this->get('Doctrine.ORM.DefaultEntityManager')->flush();

            $this->get('session')->setFlash('doctrine_user_group_update', 'success');

            return $this->redirect($this->generateUrl('doctrine_user_group_show', array('name' => $form->getData()->getName())));
        }

        return $this->render('DoctrineUserBundle:Group:edit.'.$this->getRenderer());
    }

    /**
     * Deletes the specified group and redirects to the groups list
     */
    public function deleteAction($name)
    {
        $group = $this->findGroup($name);

        $this->get('doctrine_user.repository.group')->getObjectManager()->delete($group);
        $this->get('doctrine_user.repository.group')->getObjectManager()->flush();

        $this->get('session')->setFlash('doctrine_user_group_delete/success');

        return $this->redirect($this->generateUrl('doctrine_user_group_list'));
    }

    /**
     * Find a group by its name
     *
     * @param string $name
     * @throw NotFoundException if user does not exist
     * @return Group
     */
    protected function findGroup($name)
    {
        if (empty($name)) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist', $name));
        }
        $group = $this->get('doctrine_user.repository.group')->findOneByName($name);
        if (!$group) {
            throw new NotFoundHttpException(sprintf('The group "%s" does not exist', $name));
        }

        return $group;
    }

    /**
     * Create a GroupForm instance and returns it
     *
     * @param Group $object
     * @return Bundle\DoctrineUserBundle\Form\GroupForm
     */
    protected function createForm($object = null)
    {
        $form = $this->get('doctrine_user.form.group');
        if (null === $object) {
            $groupClass = $this->get('doctrine_user.repository.group')->getObjectClass();
            $object = new $groupClass();
        }

        $form->setData($object);

        return $form;
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
