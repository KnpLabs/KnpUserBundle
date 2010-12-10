<?php

namespace Bundle\DoctrineUserBundle\Model;

interface RoleHolderInterface
{
    const ROLE_DEFAULT    = 'ROLE_USER';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    
    function getRoles();
    function hasRole($role);
}