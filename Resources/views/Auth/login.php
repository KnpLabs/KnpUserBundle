<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view->session->hasFlash('loginError')): ?>
<p>Bad username or password, please try again.</p>
<?php endif; ?>

<form method="post" action="<?php echo $view->router->generate('login') ?>" name="loginForm">

    <label for="username">Username</label>
    <input type="text" id="username" name="username" />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" />

    <input type="submit" value="Log in" />
    
</form>
