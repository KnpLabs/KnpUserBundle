<?php

$userClass = $this->container->get('doctrine_user.user_repository')->getObjectClass();

$admin = new $userClass();
$admin->setUsername('admin');
$admin->setEmail('admin@site.org');
$admin->setPassword('admin');
$admin->setIsSuperAdmin(true);

for($it = 1; $it <= 5; $it++) {
    ${'user'.$it} = new $userClass();
    ${'user'.$it}->setUsername('user'.$it);
    ${'user'.$it}->setEmail('user'.$it.'@site.org');
    ${'user'.$it}->setPassword('password'.$it);
}
