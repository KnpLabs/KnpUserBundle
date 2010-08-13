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

use Symfony\Bundle\FrameworkBundle\Controller as Controller;
use Symfony\Components\EventDispatcher\Event;

class AuthController extends Controller
{

    public function loginAction()
    {
        $this->getSession()->start();
        $request = $this->getRequest();

        if('POST' === $request->getMethod()) {
            $username = $request->get('username');
            $password = $request->get('password');
            
            $user = $this->getDoctrineUser()
            ->getRepository($this->container->getParameter('doctrine_user.user_object.class'))
            ->findOneByUsernameAndPassword($username, $password);

            if($user && $user->getIsActive())
            {
                $event = new Event($this, 'doctrine_user.login', array('user' => $user));
                $this->container->getEventDispatcherService()->notify($event);

                return $this->redirect($this->generateUrl('loginSuccess'));
            }
            else
            {
                $this->getSession()->setFlash('loginError', true);
            }
        }

        $view = $this->container->getParameter('doctrine_user.view.login');
        return $this->render($view, array());
    }

    public function logoutAction()
    {
        $this->getSession()->start();
        if($user = $this->getSession()->getAttribute('identity'))
        {
            $event = new Event($this, 'doctrine_user.logout', array('user' => $user));
            $this->container->getEventDispatcherService()->notify($event);
        }

        return $this->redirect($this->generateUrl('login'));
    }

    public function successAction()
    {
        $this->getSession()->start();
        $identity = $this->getSession()->getAttribute('identity');

        $view = $this->container->getParameter('doctrine_user.view.success');
        return $this->render($view, array(
            'identity' => $identity,
        ));
    }

    protected function getSession()
    {
        return $this->container->getSessionService();
    }

    protected function getEntityManager()
    {
        return $this->container->getDoctrine_ORM_EntityManagerService();
    }

}
