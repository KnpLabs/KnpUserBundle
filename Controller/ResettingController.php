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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use FOS\UserBundle\Model\UserInterface;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends ContainerAware
{
    /**
     * Request reset user password: show form
     */
    public function requestAction()
    {
        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.'.$this->getEngine());
    }

    /**
     * Request reset user password: submit form and send email
     */
    public function sendEmailAction()
    {
        $user = $this->findUserBy('username', $this->container->get('request')->get('username'));

        if ($user->isPasswordRequestNonExpired($this->getPasswordRequestTtl())) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
        }

        $user->generateConfirmationToken();
        $this->container->get('session')->set('fos_user_send_resetting_email/email', $user->getEmail());
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user, $this->getEngine());
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_check_email'));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_resetting_email/email');
        $this->container->get('session')->remove('fos_user_send_resetting_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);
        if (empty($user)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $this->setFlash('fos_user_resetting', 'success');

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Reset user password
     */
    public function resetAction($token)
    {
        $user = $this->findUserBy('confirmationToken', $token);

        if (!$user->isPasswordRequestNonExpired($this->getPasswordRequestTtl())) {
            return new RedirectResponse( $this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.form.reset_password');
        $formHandler = $this->container->get('fos_user.form.handler.reset_password');
        $process = $formHandler->process($user);

        if ($process) {
            $this->authenticateUser($user);

            $this->setFlash('fos_user_resetted', 'success');
            $url =  $this->container->get('router')->generate('fos_user_user_show', array('username' => $user->getUsername()));
            return new RedirectResponse($url);
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView()
        ));
    }

    /**
     * Find a user by a specific property
     *
     * @param string $key property name
     * @param mixed $value property value
     * @throws NotFoundException if user does not exist
     * @return UserInterface
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

    /**
     * Authenticate a user with Symfony Security
     *
     * @param UserInterface $user
     * @param Boolean $reAuthenticate
     */
    protected function authenticateUser(UserInterface $user, $reAuthenticate = false)
    {
        $providerKey = $this->container->getParameter('fos_user.firewall_name');
        $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());

        if (true === $reAuthenticate) {
            $token->setAuthenticated(false);
        }

        $this->container->get('security.context')->setToken($token);
    }

    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    protected function getPasswordRequestTtl()
    {
        return $this->container->getParameter('fos_user.email.resetting_password.token_ttl');
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
}
