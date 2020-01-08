<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Tests;

@trigger_error('Using Groups is deprecated since version 2.2 and will be removed in 3.0.', E_USER_DEPRECATED);

use FOS\UserBundle\Model\Group;

/**
 * @deprecated
 */
class TestGroup extends Group
{
    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
