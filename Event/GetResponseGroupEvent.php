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

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

use Symfony\Component\HttpFoundation\Response;

/**
 * @final
 *
 * @deprecated
 */
class GetResponseGroupEvent extends GroupEvent
{
    /**
     * @var Response
     */
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
