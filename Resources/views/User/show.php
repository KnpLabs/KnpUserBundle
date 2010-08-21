<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view['session']->hasFlash('doctrine_user_user_create/success')): ?>
<div class="doctrine_user_user_create_success">The user has been created successfully</div>
<?php endif; ?>

<div class="doctrine_user_user_show">
    <p>Username: <?php echo $user->getUsername() ?></p>
    <p>Email: <?php echo $user->getEmail() ?></p>
</div>
