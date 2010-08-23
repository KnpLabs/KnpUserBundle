<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller as Controller;
use Bundle\DoctrineUserBundle\DAO\User;

/**
 * RESTful controller managing authentication: login and logout
 */
class SessionController extends Controller
{
    /**
     * Show the login form
     */
    public function newAction()
    {
        $form = $this['doctrine_user.session_form'];

        if ($this['request']->headers->has('HTTP_REFERER')) {
            $this['session']->set('DoctrineUserBundle/referer', $this['request']->headers->get('HTTP_REFERER'));
        }

        return $this->render('DoctrineUserBundle:Session:new', compact('form'));
    }

    /**
     * Log in the user 
     */
    public function createAction()
    {
        $form = $this['doctrine_user.session_form'];
        $data = $this['request']->request->get($form->getName());
        $user = $this['doctrine_user.user_repository']->findOneByUsernameOrEmail($data['usernameOrEmail']);

        if($user && $user->checkPassword($data['password']) && $user->isAllowedToLogin())
        {
            $this['doctrine_user.auth']->login($user);

            $this['session']->setFlash('success', 'Welcome back ' . $user->getUsername());

            return $this->redirect($this['session']->get('DoctrineUserBundle/referer', $this->generateUrl('homepage'));
        }

        $form->addError('The entered username and/or password is invalid.');

        return $this->render('DoctrineUserBundle:Session:new', compact('form'));
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
