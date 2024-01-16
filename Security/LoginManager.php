<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Security;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeHandlerInterface;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * Abstracts process for manually logging in a user.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class LoginManager implements LoginManagerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var SessionAuthenticationStrategyInterface
     */
    private $sessionStrategy;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RememberMeHandlerInterface|RememberMeServicesInterface|null
     */
    private $rememberMeHandler;

    /**
     * @param RememberMeHandlerInterface|RememberMeServicesInterface|null $rememberMeHandler
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserCheckerInterface $userChecker,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        RequestStack $requestStack,
        $rememberMeHandler = null
    ) {
        if (null !== $rememberMeHandler && !$rememberMeHandler instanceof RememberMeHandlerInterface && !$rememberMeHandler instanceof RememberMeServicesInterface) {
            throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be an instance of "%s|%s|null", "%s" given.', __METHOD__, RememberMeHandlerInterface::class, RememberMeServicesInterface::class, \is_object($rememberMeHandler) ? \get_class($rememberMeHandler) : \gettype($rememberMeHandler)));
        }

        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->sessionStrategy = $sessionStrategy;
        $this->requestStack = $requestStack;
        $this->rememberMeHandler = $rememberMeHandler;
    }

    final public function logInUser($firewallName, UserInterface $user, Response $response = null): void
    {
        $this->userChecker->checkPreAuth($user);

        $token = $this->createToken($firewallName, $user);
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request) {
            $this->sessionStrategy->onAuthentication($request, $token);

            if (null !== $response && $this->rememberMeHandler instanceof RememberMeServicesInterface) {
                $this->rememberMeHandler->loginSuccess($request, $response, $token);
            } elseif ($this->rememberMeHandler instanceof RememberMeHandlerInterface) {
                $this->rememberMeHandler->createRememberMeCookie($user);
            }
        }

        $this->tokenStorage->setToken($token);
    }

    /**
     * @param string $firewall
     *
     * @return UsernamePasswordToken
     */
    protected function createToken($firewall, UserInterface $user)
    {
        // Bc layer for Symfony <5.4
        if (!interface_exists(CacheableVoterInterface::class)) {
            return new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        }

        return new UsernamePasswordToken($user, $firewall, $user->getRoles());
    }
}
