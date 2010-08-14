<?php $view->extend('DoctrineUserBundle::layout'); ?>

<h1>Hello, <?php echo $identity->getUsername(); ?>!</h1>

You have been logged in successfully.

<a href="<?php echo $view->router->generate('logout') ?>">
    Log out, now
</a>