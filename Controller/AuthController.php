<?php

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
                $this->getUser()->setAttribute('identity', $user);

                $user->login();
                $this->getEntityManager()->flush();

                $event = new Event($this, 'doctrine_user.login', array('user' => $user));
                $this->container->eventDispatcher->notify($event);

                return $this->redirect($this->generateUrl('loginSuccess'));
            }
            else
            {
                $this->getUser()->setFlash('loginError', true);
            }
        }

        return $this->render('DoctrineUserBundle:Auth:login', array());
    }

    public function logoutAction()
    {
        if($user = $this->getUser()->getAttribute('identity'))
        {
            $this->getUser()->setAttribute('identity', null);

            $event = new Event($this, 'doctrine_user.logout', array('user' => $user));
            $this->container->eventDispatcher->notify($event);
        }

        return $this->redirect($this->generateUrl('login'));
    }

    public function successAction()
    {
        $identity = $this->getUser()->getAttribute('identity');

        return $this->render('DoctrineUserBundle:Auth:success', array(
            'identity' => $identity,
        ));
    }

}