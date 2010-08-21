<?php $view->extend('DoctrineUserBundle::layout'); ?>

<div class="doctrine_user_session_create_success">
    <h1>Hello, <?php echo $view->auth->getUser()->getUsername(); ?>!</h1>
    You have been logged in successfully.
    <a href="<?php echo $view['router']->generate('doctrine_user_session_delete') ?>">Log out</a>
</div>
