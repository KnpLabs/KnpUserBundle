<?php

use Bundle\DoctrineUserBundle\Entities\User as User;

$admin = new User();
$admin->setUsername('admin');
$admin->setPassword('admin');
$admin->setIsSuperAdmin(true);

$knplabs = new User();
$knplabs->setUsername('knplabs');
$knplabs->setPassword('changeme');

$zenzile = new User();
$zenzile->setUsername('zenzile');
$zenzile->setPassword('changeme');

$hightone = new User();
$hightone->setUsername('hightone');
$hightone->setPassword('changeme');

$interlope = new User();
$interlope->setUsername('interlope');
$interlope->setPassword('changeme');