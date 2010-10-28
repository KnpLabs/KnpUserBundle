<?php

// Permissions

$permissionClass = $this->container->get('doctrine_user.repository.permission')->getObjectClass();

$nbPermissions = 5;
for ($it = 1; $it <= $nbPermissions; $it++) {
    ${'permission'.$it} = new $permissionClass();
    ${'permission'.$it}->setName('permission'.$it);
    ${'permission'.$it}->setDescription('Permission number #'.$it);
}

// Groups

$groupClass = $this->container->get('doctrine_user.repository.group')->getObjectClass();

$nbGroups = 5;
for ($it = 1; $it <= $nbGroups; $it++) {
    ${'group'.$it} = new $groupClass();
    ${'group'.$it}->setName('group'.$it);
    ${'group'.$it}->setDescription('Group number #'.$it);
}

// Users

$userClass = $this->container->get('doctrine_user.repository.user')->getObjectClass();

$admin = new $userClass();
$admin->setUsername('admin');
$admin->setEmail('admin@site.org');
$admin->setPassword('admin');
$admin->setIsSuperAdmin(true);
$admin->setIsActive(true);

$nbUsers = 5;
for ($it = 1; $it <= $nbUsers; $it++) {
    ${'user'.$it} = new $userClass();
    ${'user'.$it}->setUsername('user'.$it);
    ${'user'.$it}->setEmail('user'.$it.'@site.org');
    ${'user'.$it}->setPassword('password'.$it);
}

// User permissions

$user1->addPermission($permission1);
$user1->addPermission($permission2);

// Group permissions

for ($it = 1; $it <= $nbPermissions; $it++) {
    for ($jt = 0; $jt <= 1; $jt++) {
        ${'group'.$it}->addPermission(${'permission'.(($it+$jt)%$nbPermissions+1)});
    }
}

// User groups

for ($it = 1; $it <= $nbUsers; $it++) {
    for ($jt = 0; $jt <= 1; $jt++) {
        ${'user'.$it}->addGroup(${'group'.(($it+$jt)%$nbGroups+1)});
    }
}
