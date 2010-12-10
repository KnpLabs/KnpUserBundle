<?php

namespace Bundle\DoctrineUserBundle\Model;

interface MutableRoleHolderInterface extends RoleHolderInterface
{
    function setRoles(array $roles);
    function addRole($role);
    function removeRole($role);
}