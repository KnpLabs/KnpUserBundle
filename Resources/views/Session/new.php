<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view->session->hasFlash('doctrine_user_session_new/error')): ?>
<p>Bad username or password, please try again.</p>
<?php endif; ?>

<form method="PUT" action="<?php echo $view->router->generate('doctrine_user_session_create') ?>" name="doctrine_user_session_new">

    <label for="username">Username</label>
    <input type="text" id="username" name="username" />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" />

    <input type="submit" value="Log in" />
    
</form>
