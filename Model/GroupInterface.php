<?php

/**
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 * (c) Christophe Coevoet <stof@notk.org>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Model;

interface GroupInterface
{
    function addRole($role);

    function getId();

    function getName();

    function hasRole($role);

    function getRoles();

    function removeRole($role);

    function setName($name);

    function setRoles(array $roles);
}