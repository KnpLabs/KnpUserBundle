<?php

/**
 * (c) Matthieu Bontemps <matthieu@knplabs.com>
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Henrik Bjornskov <henrik@bearwoods.dk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\Event;
use Bundle\DoctrineUserBundle\DAO\User;

/**
 * RESTful controller managing authentication: login and logout
 */
class SessionController extends Controller
{
    /**
     * Renders the login form. And saves the referer on the user.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $form = $this['doctrine_user.session_form'];

        if ($this['request']->server->has('HTTP_REFERER')) {
            $this['session']->set('DoctrineUserBundle/referer', $this['request']->server->get('HTTP_REFERER'));
        }

        return $this->render('DoctrineUserBundle:Session:new:'.$this->getRenderer(), compact('form'));
    }

    /**
     * Logs in the user and upon success notify the event doctrine_user.login_success.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $form = $this['doctrine_user.session_form'];
        $data = $this['request']->request->get($form->getName());
        $user = $this['doctrine_user.user_repository']->findOneByUsernameOrEmail($data['usernameOrEmail']);


        if ($user && $user->checkPassword($data['password'])) {
            $event = new Event($this, 'doctrine_user.user_can_login_filter', array());
            $this['event_dispatcher']->filter($event, true);

            if ($event->getReturnValue()) {
                $this['doctrine_user.auth']->login($user);

                $event = new Event($this, 'doctrine_user.login_success', array('user' => $user));
                $this['event_dispatcher']->notifyUntil($event);

                if ($event->isProcessed()) {
                    return $event->getReturnValue();
                }

                return $this->redirect(
                    $this['session']->get('DoctrineUserBundle/referer', $this->generateUrl(
                        $this->container->getParameter('doctrine_user.session_create.success_route')
                    ))
                );
            }
        }

        $this['session']->setFlash('doctrine_user_session_create/error', true);

        $form->addError('The entered username and/or password is invalid.');

        return $this->render('DoctrineUserBundle:Session:new:'.$this->getRenderer(), compact('form'));
    }
    
    public function successAction()
    {
        if (!$this['doctrine_user.auth']->isAuthenticated()) {
            return $this->redirect($this->generateUrl('doctrine_user_session_new'));
        }

        return $this->render('DoctrineUserBundle:Session:success:'.$this->getRenderer());
    }

    /**
     * Deletes the current session.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction()
    {
        $this['doctrine_user.auth']->logout();
        return $this->redirect($this->generateUrl('doctrine_user_session_new'));
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
