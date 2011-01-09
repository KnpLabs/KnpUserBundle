<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bundle\FOS\UserBundle\Model;

use Symfony\Component\Security\User\AdvancedAccountInterface;

interface UserInterface extends AdvancedAccountInterface
{
    public function addRole($role);

    public function getAlgorithm();

    public function setAlgorithm($algorithm);

    public function getPlainPassword();

    public function setPlainPassword($password);

    public function setPassword($password);

    public function isSuperAdmin();

    public function is(UserInterface $user = null);

    public function setEnabled($boolean);

    public function setSuperAdmin($boolean);

    public function getConfirmationToken();

    public function setConfirmationToken($confirmationToken);

    public function hasRole($role);

    public function setRoles(array $roles);

    public function removeRole($role);

    function getGroups();

    function getGroupNames();

    function hasGroup($name);

    function addGroup(GroupInterface $group);

    function removeGroup(GroupInterface $group);
}
