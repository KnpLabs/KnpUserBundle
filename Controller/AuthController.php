<?php

/**
 * This file is part of the Symfony framework.
 *
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Framework\DoctrineBundle\Controller\DoctrineController;
use Symfony\Components\EventDispatcher\Event;

class AuthController extends DoctrineController
{

    public function loginAction()
    {
        $request = $this->getRequest();

        if('POST' === $request->getMethod()) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            
            $user = $this->getEntityManager()
            ->getRepository('Bundle\DoctrineUserBundle\Entities\User')
            ->findOneByUsernameAndPassword($username, $password);

            if($user && $user->getIsActive())
            {
                $event = new Event($this, 'doctrine_user.login', array('user' => $user));
                $this->container->eventDispatcher->notify($event);

                return $this->redirect($this->generateUrl('loginSuccess'));
            }
            else
            {
                $this->getUser()->setFlash('loginError', true);
            }
        }

        $view = $this->container->getParameter('doctrine_user.view.login');
        return $this->render($view, array());
    }

    public function logoutAction()
    {
        if($user = $this->getUser()->getAttribute('identity'))
        {
            $event = new Event($this, 'doctrine_user.logout', array('user' => $user));
            $this->container->eventDispatcher->notify($event);
        }

        return $this->redirect($this->generateUrl('login'));
    }

    public function successAction()
    {
        $identity = $this->getUser()->getAttribute('identity');

        $view = $this->container->getParameter('doctrine_user.view.success');
        return $this->render($view, array(
            'identity' => $identity,
        ));
    }

}