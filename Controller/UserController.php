<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Controller;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bundle\FOS\UserBundle\Model\User;
use Bundle\FOS\UserBundle\Form\ChangePassword;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Exception\AccessDeniedException;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show all users
     */
    public function listAction()
    {
        $users = $this->get('fos_user.user_manager')->findUsers();

        return $this->render('FOS\UserBundle:User:list.'.$this->getRenderer(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUserBy('username', $username);
        return $this->render('FOS\UserBundle:User:show.'.$this->getRenderer(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $form = $this->createForm($user);

        return $this->render('FOS\UserBundle:User:edit.'.$this->getRenderer(), array(
            'form'      => $form,
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Update a user
     */
    public function updateAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $form = $this->createForm($user);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);
            $this->get('session')->setFlash('fos_user_user_update', 'success');
            $userUrl = $this->generateUrl('fos_user_user_show', array('username' => $user->getUsername()));
            return $this->redirect($userUrl);
        }

        return $this->render('FOS\UserBundle:User:edit.'.$this->getRenderer(), array(
            'form'      => $form,
            'username'  => $user->getUsername()
        ));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->createForm();

        return $this->render('FOS\UserBundle:User:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Create a user and send a confirmation email
     */
    public function createAction()
    {
        $form = $this->createForm();
        $form->setValidationGroups('Registration');
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $user = $form->getData();
            if ($this->container->getParameter('fos_user.confirmation_email.enabled')) {
                $user->setEnabled(false);
                $this->get('fos_user.user_manager')->updateUser($user);
                $this->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $url = $this->generateUrl('fos_user_user_send_confirmation_email');
            } else {
                $user->setEnabled(true);
                $this->get('fos_user.user_manager')->updateUser($user);
                $this->authenticateUser($user);
                $url = $this->generateUrl('fos_user_user_confirmed');
            }

            if ($this->container->has('security.acl.provider')) {
                $provider = $this->container->get('security.acl.provider');
                $acl = $provider->createAcl(ObjectIdentity::fromDomainObject($user));
                $acl->insertObjectAce(UserSecurityIdentity::fromAccount($user), MaskBuilder::MASK_OWNER);
                $provider->updateAcl($acl);
            }

            $this->get('session')->setFlash('fos_user_user_create', 'success');
            return $this->redirect($url);
        }

        return $this->render('FOS\UserBundle:User:new.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Send the confirmation email containing a link to the confirmation page,
     * then redirect the check email page
     */
    public function sendConfirmationEmailAction()
    {
        if (!$this->container->getParameter('fos_user.confirmation_email.enabled')) {
            throw new NotFoundHttpException('Email confirmation is disabled');
        }

        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $user = $this->findUserBy('email', $email);
        $this->sendConfirmationEmailMessage($user);

        return $this->redirect($this->generateUrl('fos_user_user_check_confirmation_email'));
    }

    protected function sendConfirmationEmailMessage(User $user)
    {
        $template = $this->container->getParameter('fos_user.confirmation_email.template');
        // Render the email, use the first line as the subject, and the rest as the body
        $rendered = $this->renderView($template.'.'.$this->getRenderer(), array(
            'user' => $user,
            'confirmationUrl' => $this->generateUrl('fos_user_user_confirm', array('token' => $user->getConfirmationToken()), true)
        ));
        $renderedLines = explode("\n", trim($rendered));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));
        $fromEmail = $this->container->getParameter('fos_user.confirmation_email.from_email');

        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($user->getEmail())
            ->setBody($body);

        $mailer->send($message);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkConfirmationEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $user = $this->findUserBy('email', $email);

        return $this->render('FOS\UserBundle:User:checkConfirmationEmail.'.$this->getRenderer(), array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);
        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $this->get('fos_user.user_manager')->updateUser($user);
        $this->authenticateUser($user);

        return $this->redirect($this->generateUrl('fos_user_user_confirmed'));
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        return $this->render('FOS\UserBundle:User:confirmed.'.$this->getRenderer(), array(
            'user' => $user,
        ));
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUserBy('username', $username);
        $this->get('fos_user.user_manager')->deleteUser($user);
        $this->get('session')->setFlash('fos_user_user_delete', 'success');

        return $this->redirect($this->generateUrl('fos_user_user_list'));
    }

    /**
     * Change user password: show form
     */
    public function changePasswordAction()
    {
        $user = $this->getUser();
        $form = $this->createChangePasswordForm($user);

        return $this->render('FOS\UserBundle:User:changePassword.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Change user password: submit form
     */
    public function changePasswordUpdateAction()
    {
        $user = $this->getUser();
        $form = $this->createChangePasswordForm($user);
        $form->bind($this->get('request')->request->get($form->getName()));

        if ($form->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($user);
            $userUrl = $this->generateUrl('fos_user_user_show', array('username' => $user->getUsername()));

            return $this->redirect($userUrl);
        }

        return $this->render('FOS\UserBundle:User:changePassword.'.$this->getRenderer(), array(
            'form' => $form
        ));
    }

    /**
     * Get a user from the security context
     *
     * @throws AccessDeniedException if no user is authenticated
     * @return User
     */
    protected function getUser()
    {
        $user = $this->get('security.context')->getUser();
        if (!$user) {
            throw new AccessDeniedException('A logged in user is required.');
        }

        return $user;
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
            $user = $this->get('fos_user.user_manager')->{'findUserBy'.ucfirst($key)}($value);
        }

        if (empty($user)) {
            throw new NotFoundHttpException(sprintf('The user with "%s" does not exist for value "%s"', $key, $value));
        }

        return $user;
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param Boolean $reAuthenticate
     * @return null
     */
    protected function authenticateUser(User $user, $reAuthenticate = false)
    {
        $token = new UsernamePasswordToken($user, null, $user->getRoles());

        if (true === $reAuthenticate) {
            $token->setAuthenticated(false);
        }

        $this->get('security.context')->setToken($token);
    }

    /**
     * Create a UserForm instance and returns it
     *
     * @param User $object
     * @return Bundle\FOS\UserBundle\Form\UserForm
     */
    protected function createForm($object = null)
    {
        $form = $this->get('fos_user.form.user');
        if (null === $object) {
            $object = $this->get('fos_user.user_manager')->createUser();
        }

        $form->setData($object);

        return $form;
    }

    protected function createChangePasswordForm(User $user)
    {
        $form = $this->get('fos_user.form.change_password');
        $form->setData(new ChangePassword($user));

        return $form;
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('fos_user.template.renderer');
    }
}
