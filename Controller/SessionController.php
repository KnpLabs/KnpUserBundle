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
use Bundle\DoctrineUserBundle\Model\User;
use Symfony\Component\HttpFoundation\Response;

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
        $form = $this->get('doctrine_user.form.session');

        if ($this->get('request')->server->has('HTTP_REFERER')) {
            $this->get('session')->set('DoctrineUserBundle/referer', $this->get('request')->server->get('HTTP_REFERER'));
        }

        return $this->render('DoctrineUserBundle:Session:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Logs in the user and upon success notify the event doctrine_user.login_success.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $form = $this->get('doctrine_user.form.session');
        $data = $this->get('request')->request->get($form->getName());
        $user = $this->get('doctrine_user.repository.user')->findOneByUsernameOrEmail($data['usernameOrEmail']);

        if ($user && $user->getIsActive() && $user->checkPassword($data['password'])) {
            $event = new Event($this, 'doctrine_user.user_can_login_filter', array());
            $this->get('event_dispatcher')->filter($event, true);

            if ($event->getReturnValue()) {
                $this->get('doctrine_user.auth')->login($user);

                $event = new Event($this, 'doctrine_user.login_success', array('user' => $user));
                $this->get('event_dispatcher')->notifyUntil($event);

                if ($event->isProcessed()) {
                    return $event->getReturnValue();
                }

                $response = $this->redirect(
                    $this->get('session')->get('DoctrineUserBundle/referer', $this->generateUrl(
                        $this->container->getParameter('doctrine_user.session_create.success_route')
                    ))
                );
                $this->storeRememberMeCookie($user, $response, isset($data['rememberMe']));

                return $response;
            }
        }

        $this->get('session')->setFlash('doctrine_user_session_create/error', true);

        $form->addError('The entered username and/or password is invalid.');

        return $this->render('DoctrineUserBundle:Session:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    protected function storeRememberMeCookie(User $user, Response $response, $rememberMe)
    {
        $rememberMeCookieValue = $rememberMe ?$user->getRememberMeToken() : null;
        $rememberMeCookieName = $this->get('doctrine_user.auth')->getOption('remember_me_cookie_name');
        $rememberMeLifetime = $this->get('doctrine_user.auth')->getOption('remember_me_lifetime');
        $response->headers->setCookie($rememberMeCookieName, $rememberMeCookieValue, null, time() + $rememberMeLifetime);
    }

    public function successAction()
    {
        if (!$this->get('doctrine_user.auth')->isAuthenticated()) {
            return $this->redirect($this->generateUrl('doctrine_user_session_new'));
        }

        return $this->render('DoctrineUserBundle:Session:success.'.$this->getRenderer());
    }

    /**
     * Deletes the current session.
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction()
    {
        $this->get('doctrine_user.auth')->logout();
        return $this->redirect($this->generateUrl('doctrine_user_session_new'));
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
