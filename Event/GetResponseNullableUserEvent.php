<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Response user event that allows null user.
 *
 * @author Konstantinos Christofilos <kostas.christofilos@gmail.com>
 * @final
 */
class GetResponseNullableUserEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(?UserInterface $user, Request $request)
    {
        $this->user = $user;
        $this->request = $request;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
