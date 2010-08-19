<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller as Controller;
use Bundle\DoctrineUserBundle\DAO\User;
use Symfony\Components\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this['doctrine_user.user_repository']->findOneByUsername($username);
        if(!$user) {
            throw new NotFoundHttpException(sprintf('The user "%s" does not exist', $username));
        }

        return $this->render('DoctrineUserBundle:User:show', array('user' => $user));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        return $this->render('DoctrineUserBundle:User:new', array('form' => $this->createForm('doctrine_user_user_new')));
    }

    /**
     * Create a user
     */
    public function createAction()
    {
        $form = $this->createForm('doctrine_user_user_new');

        if($this['request']->request->has($form->getName())) {
            $form->bind($this['request']->request->get($form->getName()));
            if($form->isValid()) {
                $user = $form->getData();
                $this['doctrine_user.user_repository']->getObjectManager()->persist($user);
                $this['doctrine_user.user_repository']->getObjectManager()->flush();
                $this['session']->start();
                $this['session']->setFlash('doctrine_user_user_create/success', true);
                $userUrl = $this->generateUrl('doctrine_user_user_show', array('username' => $user->getUsername()));
                return $this->redirect($userUrl);
            }
        }

        return $this->render('DoctrineUserBundle:User:new', array('form' => $form));
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
}
