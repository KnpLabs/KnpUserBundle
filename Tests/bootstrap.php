<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists($file = __DIR__.'/autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/autoload.php.dist')) {
    require_once $file;
}

if (class_exists('PropelQuickBuilder')) {
    $builder = new \PropelQuickBuilder();
    $builder->setSchema(file_get_contents(__DIR__.'/../Resources/config/propel/schema.xml'));
    $builder->buildClasses();
}
