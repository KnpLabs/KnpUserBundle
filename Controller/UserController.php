<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Bundle\DoctrineUserBundle\DAO\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show all users
     **/
    public function listAction()
    {
        $users = $this['doctrine_user.user_repository']->findAll();

        return $this->render('DoctrineUserBundle:User:list:'.$this->getRenderer(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUser($username);

        return $this->render('DoctrineUserBundle:User:show:'.$this->getRenderer(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUser($username);
        $form = $this->createForm('doctrine_user_user_edit', $user);

        return $this->render('DoctrineUserBundle:User:edit:'.$this->getRenderer(), array('form' => $form, 'username' => $username));
    }

    /**
     * Update a user
     */
    public function updateAction($username)
    {
        $user = $this->findUser($username);
        $form = $this->createForm('doctrine_user_user_edit', $user);

        if($data = $this['request']->request->get($form->getName())) {
            $form->bind($data);
            if($form->isValid()) {
                $this->saveUser($user);
                $this['session']->start();
                $this['session']->setFlash('doctrine_user_user_update/success', true);
                $userUrl = $this->generateUrl('doctrine_user_user_show', array('username' => $user->getUsername()));
                return $this->redirect($userUrl);
            }
        }

        return $this->render('DoctrineUserBundle:User:edit:'.$this->getRenderer(), array('form' => $form, 'username' => $username));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->createForm('doctrine_user_user_new');

        return $this->render('DoctrineUserBundle:User:new:'.$this->getRenderer(), array('form' => $form));
    }

    /**
     * Create a user
     */
    public function createAction()
    {
        $form = $this->createForm('doctrine_user_user_new');
        $form->bind($this['request']->request->get($form->getName()));

        if($form->isValid()) {
            $user = $form->getData();
            $this->saveUser($user);
            $this['session']->setFlash('doctrine_user_user_create/success', true);
            $userUrl = $this->generateUrl('doctrine_user_user_show', array('username' => $user->getUsername()));
            return $this->redirect($userUrl);
        }

        return $this->render('DoctrineUserBundle:User:new:'.$this->getRenderer(), array('form' => $form));
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUser($username);

        $objectManager = $this['doctrine_user.user_repository']->getObjectManager();
        $objectManager->remove($user);
        $objectManager->flush();
        $this['session']->start();
        $this['session']->setFlash('doctrine_user_user_delete/success', true);

        return $this->redirect($this->generateUrl('doctrine_user_user_list'));
    }

    /**
     * Find a user by its username
     * 
     * @param string $username 
     * @throw NotFoundException if user does not exist
     * @return User
     */
    protected function findUser($username)
    {
        $user = $this['doctrine_user.user_repository']->findOneByUsername($username);
        if(!$user) {
            throw new NotFoundHttpException(sprintf('The user "%s" does not exist', $username));
        }
        
        return $user;
    }

    /**
     * Save a user in database
     *
     * @param User $user
     * @return null
     **/
    public function saveUser(User $user)
    {
        $objectManager = $this['doctrine_user.user_repository']->getObjectManager();
        $objectManager->persist($user);
        $objectManager->flush();
    }

    /**
     * Create a UserForm instance and returns it 
     * 
     * @param string $name 
     * @param User $object 
     * @return Bundle\DoctrineUserBundle\Form\UserForm
     */
    protected function createForm($name, $object = null)
    {
        $formClass = $this->container->getParameter('doctrine_user.user_form.class');
        if(null === $object) {
            $userClass = $this['doctrine_user.user_repository']->getObjectClass();
            $object = new $userClass();
        }

        return new $formClass($name, $object, $this['validator']);
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
