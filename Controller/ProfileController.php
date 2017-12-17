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

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\User;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Services\EmailConfirmation\EmailUpdateConfirmation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\Translator;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends AbstractController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
    }

    /**
     * Show the user.
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $dispatcher = $this->eventDispatcher;

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/Profile/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Confirm user`s email update.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmEmailUpdateAction(Request $request, $token)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->findUserByConfirmationToken($token);

        // If user was not found throw 404 exception
        if (!$user) {
            /** @var Translator $translator */
            $translator = $this->get('translator');
            throw $this->createNotFoundException($translator->trans('email_update.error.message', array(), 'FOSUserBundle'));
        }

        // Show invalid token message if the user id found via token does not match the current users id (e.g. anon. or other user)
        if (!($this->getUser() instanceof UserInterface) || ($user->getId() !== $this->getUser()->getId())) {
            /** @var Translator $translator */
            $translator = $this->get('translator');
            throw new AccessDeniedException($translator->trans('email_update.error.message', array(), 'FOSUserBundle'));
        }

        /** @var EmailUpdateConfirmation $emailUpdateConfirmation */
        $emailUpdateConfirmation = $this->get('fos_user.email_update_confirmation');

        $emailUpdateConfirmation->setUser($user);

        $newEmail = $emailUpdateConfirmation->fetchEncryptedEmailFromConfirmationLink($request->get('target'));

        // Update user email
        if ($newEmail) {
            $user->setConfirmationToken($emailUpdateConfirmation->getEmailConfirmedToken());
            $user->setEmail($newEmail);
        }

        $userManager->updateUser($user);

        $event = new UserEvent($user, $request);
        $this->get('event_dispatcher')->dispatch(FOSUserEvents::EMAIL_UPDATE_SUCCESS, $event);

        return $this->redirect($this->generateUrl('fos_user_profile_show'));
    }
}
