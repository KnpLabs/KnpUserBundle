<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Model\UserInterface;

/**
 * RESTful controller managing user CRUD
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserController extends ContainerAware
{
    /**
     * Show all users
     */
    public function listAction()
    {
        $users = $this->container->get('fos_user.user_manager')->findUsers();

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:list.html.'.$this->getEngine(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUserBy('username', $username);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:show.html.'.$this->getEngine(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $form = $this->container->get('fos_user.form.user');
        $formHandler = $this->container->get('fos_user.form.handler.user');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_user_update', 'success');
            $userUrl =  $this->container->get('router')->generate('fos_user_user_show', array('username' => $user->getUsername()));

            return new RedirectResponse($userUrl);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:User:edit.html.'.$this->getEngine(), array(
            'form'      => $form->createView(),
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $this->container->get('fos_user.user_manager')->deleteUser($user);
        $this->setFlash('fos_user_user_delete', 'success');

        return new RedirectResponse( $this->container->get('router')->generate('fos_user_user_list'));
    }

    /**
     * Find a user by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     * @throws NotFoundException if user does not exist
     * @return User
     */
    protected function findUserBy($key, $value)
    {
        if (!empty($value)) {
            $user = $this->container->get('fos_user.user_manager')->{'findUserBy'.ucfirst($key)}($value);
        }

        if (empty($user)) {
            throw new NotFoundHttpException(sprintf('The user with "%s" does not exist for value "%s"', $key, $value));
        }

        return $user;
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
}
