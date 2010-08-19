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

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show the new form
     */
    public function newAction()
    {
        $formClass = $this->container->getParameter('doctrine_user.user_form.class');
        $userClass = $this['doctrine_user.user_repository']->getObjectClass();
        $form = new $formClass('doctrine_user_user_new', new $userClass(), $this['validator']);
        return $this->render('DoctrineUserBundle:User:new', array('form' => $form));
    }

    /**
     * Log in the user 
     */
    public function createAction()
    {
        $this['session']->start();
        $data = $this['request']->request->get($this->container->getParameter('doctrine_user.session_form.name'));
        $user = $this['doctrine_user.user_repository']->findOneByUsernameOrEmail($data['usernameOrEmail']);

        if($user && $user->getIsActive() && $user->checkPassword($data['password']))
        {
            $this['doctrine_user.auth']->login($user);

            $this['session']->setFlash('doctrine_user_session_new/success', true);
            return $this->onCreateSuccess($user);
        }

        $this['session']->setFlash('doctrine_user_session_new/error', true);

        return $this->forward('DoctrineUserBundle:Session:new');
    }

    /**
     * What to do when a user successfuly logged in 
     */
    protected function onCreateSuccess(User $user)
    {   
    	$successRoute = $this->container->getParameter('doctrine_user.session_create.success_route');
    	$url = $this->generateUrl($successRoute);
        return $this->redirect($url);
    }

    public function successAction()
    {
        if(!$this['doctrine_user.auth']->isAuthenticated()) {
            return $this->redirect($this->generateUrl('doctrine_user_session_new'));
        }
        return $this->render('DoctrineUserBundle:Session:success');
    }

    /**
     * Log out the user 
     */
    public function deleteAction()
    {
        $this['doctrine_user.auth']->logout();

        return $this->redirect($this->generateUrl('doctrine_user_session_new'));
    }

}
