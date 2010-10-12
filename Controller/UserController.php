<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\DoctrineUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use Bundle\DoctrineUserBundle\DAO\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * RESTful controller managing user CRUD
 */
class UserController extends Controller
{
    /**
     * Show all users
     **/
    public function listAction()
    {
        $users = $this['doctrine_user.user_repository']->findAll();

        return $this->render('DoctrineUserBundle:User:list.'.$this->getRenderer(), array('users' => $users));
    }

    /**
     * Show one user
     */
    public function showAction($username)
    {
        $user = $this->findUser($username);

        return $this->render('DoctrineUserBundle:User:show.'.$this->getRenderer(), array('user' => $user));
    }

    /**
     * Edit one user, show the edit form
     */
    public function editAction($username)
    {
        $user = $this->findUser($username);
        $form = $this->createForm('doctrine_user_user_edit', $user);

        return $this->render('DoctrineUserBundle:User:edit.'.$this->getRenderer(), array('form' => $form, 'username' => $username));
    }

    /**
     * Update a user
     */
    public function updateAction($username)
    {
        $user = $this->findUser($username);
        $form = $this->createForm('doctrine_user_user_edit', $user);

        if ($data = $this['request']->request->get($form->getName())) {
            $form->bind($data);
            if ($form->isValid()) {
                $this->saveUser($user);
                $this['session']->setFlash('doctrine_user_user_update/success', true);
                $userUrl = $this->generateUrl('doctrine_user_user_show', array('username' => $user->getUsername()));
                return $this->redirect($userUrl);
            }
        }

        return $this->render('DoctrineUserBundle:User:edit.'.$this->getRenderer(), array('form' => $form, 'username' => $username));
    }

    /**
     * Show the new form
     */
    public function newAction()
    {
        $form = $this->createForm('doctrine_user_user_new');

        return $this->render('DoctrineUserBundle:User:new.'.$this->getRenderer(), array('form' => $form));
    }

    /**
     * Create a user and send a confirmation email
     */
    public function createAction()
    {
        $form = $this->createForm('doctrine_user_user_new');
        $form->bind($this['request']->request->get($form->getName()));

        if ($form->isValid()) {
            $user = $form->getData();
            if ($this->container->getParameter('doctrine_user.confirmation_email.enabled')) {
                $user->setIsActive(false);
                $this->saveUser($user);
                $this['session']->set('doctrine_user_send_confirmation_email/email', $user->getEmail());
                $url = $this->generateUrl('doctrine_user_user_send_confirmation_email');
            } else {
                $user->setIsActive(true);
                $this->saveUser($user);
                $this['doctrine_user.auth']->login($user);
                $url = $this->generateUrl('doctrine_user_user_confirmed');
            }

            $this['session']->setFlash('doctrine_user_user_create/success', true);
            return $this->redirect($url);
        }

        return $this->render('DoctrineUserBundle:User:new.'.$this->getRenderer(), array('form' => $form));
    }

    /**
     * Send the confirmation email containing a link to the confirmation page,
     * then redirect the check email page
     */
    public function sendConfirmationEmailAction()
    {
        if (!$this->container->getParameter('doctrine_user.confirmation_email.enabled')) {
            throw new NotFoundHttpException('Email confirmation is disabled');
        }

        $email = $this['session']->get('doctrine_user_send_confirmation_email/email');
        if (!$email) {
            throw new NotFoundHttpException(sprintf('The email "%s" does not exist', $email));
        }
        
        $user = $this['doctrine_user.user_repository']->findOneByEmail($email);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The email "%s" does not exist', $email));
        }

        $message = $this->getConfirmationEmailMessage($user);
        $this['mailer']->send($message);

        return $this->redirect($this->generateUrl('doctrine_user_user_check_confirmation_email'));
    }

    protected function getConfirmationEmailMessage(User $user)
    {
        $template = $this->container->getParameter('doctrine_user.confirmation_email.template');
        // Render the email, use the first line as the subject, and the rest as the body
        $rendered = $this->renderView($template.'.'.$this->getRenderer(), array(
            'user' => $user,
            'confirmationUrl' => $this->generateUrl('doctrine_user_user_confirm', array('token' => $user->getConfirmationToken()), true)
        ));
        $renderedLines = explode("\n", $rendered);
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $fromEmail = $this->container->getParameter('doctrine_user.confirmation_email.from_email');
	    return \Swift_Message::newInstance()
	        ->setSubject($subject)
	        ->setFrom($fromEmail)
	        ->setTo($user->getEmail())
	        ->setBody($body);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkConfirmationEmailAction()
    {
        $email = $this['session']->get('doctrine_user_send_confirmation_email/email');
        if (!$email) {
            throw new NotFoundHttpException(sprintf('The email "%s" does not exist', $email));
        }

        $user = $this['doctrine_user.user_repository']->findOneByEmail($email);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user "%s" does not exist', $email));
        }

        return $this->render('DoctrineUserBundle:User:checkConfirmationEmail.'.$this->getRenderer(), array(
            'user' => $user,
            'debug' => $this->container->getParameter('kernel.debug')
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this['doctrine_user.user_repository']->findOneByConfirmationToken($token);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('No user to confirm with token "%s"', $token));
        }

        $user->setConfirmationToken(null);
        $user->setIsActive(true);

        $this->saveUser($user);

        $this['doctrine_user.auth']->login($user);

        return $this->redirect($this->generateUrl('doctrine_user_user_confirmed'));
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this['doctrine_user.auth']->getUser();
        if (!$user) {
            throw new NotFoundHttpException(sprintf('No user confirmed'));
        }

        return $this->render('DoctrineUserBundle:User:confirmed.'.$this->getRenderer());
    }

    /**
     * Delete one user
     */
    public function deleteAction($username)
    {
        $user = $this->findUser($username);

        $objectManager = $this['doctrine_user.user_repository']->getObjectManager();
        $objectManager->remove($user);
        $objectManager->flush();
        $this['session']->setFlash('doctrine_user_user_delete/success', true);

        return $this->redirect($this->generateUrl('doctrine_user_user_list'));
    }

    /**
     * Find a user by its username
     * 
     * @param string $username 
     * @throw NotFoundException if user does not exist
     * @return User
     */
    protected function findUser($username)
    {
        if (empty($username)) {
            throw new NotFoundHttpException(sprintf('The user "%s" does not exist', $username));
        }
        $user = $this['doctrine_user.user_repository']->findOneByUsername($username);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The user "%s" does not exist', $username));
        }
        
        return $user;
    }

    /**
     * Save a user in database
     *
     * @param User $user
     * @return null
     **/
    public function saveUser(User $user)
    {
        $objectManager = $this['doctrine_user.user_repository']->getObjectManager();
        $objectManager->persist($user);
        $objectManager->flush();
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
        if (null === $object) {
            $userClass = $this['doctrine_user.user_repository']->getObjectClass();
            $object = new $userClass();
        }

        return new $formClass($name, $object, $this['validator']);
    }

    protected function getRenderer()
    {
        return $this->container->getParameter('doctrine_user.template.renderer');
    }
}
