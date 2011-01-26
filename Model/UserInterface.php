<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\UserBundle\Model;

use FOS\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Security\Core\User\AdvancedAccountInterface;

interface UserInterface extends AdvancedAccountInterface
{
    static function setCanonicalizer(CanonicalizerInterface $canonicalizer);

    function addRole($role);

    function getAlgorithm();

    function setAlgorithm($algorithm);

    function getPlainPassword();

    function setPlainPassword($password);

    function setPassword($password);

    function isSuperAdmin();

    function isUser(UserInterface $user = null);

    function setEnabled($boolean);

    function setSuperAdmin($boolean);

    function getConfirmationToken();

    function setConfirmationToken($confirmationToken);

    function hasRole($role);

    function setRoles(array $roles);

    function removeRole($role);

    function getGroups();

    function getGroupNames();

    function hasGroup($name);

    function addGroup(GroupInterface $group);

    function removeGroup(GroupInterface $group);
}
