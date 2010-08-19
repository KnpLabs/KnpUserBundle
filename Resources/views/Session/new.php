<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view->session->hasFlash('doctrine_user_session_create/error')): ?>
<div class="doctrine_user_session_create_error">Bad username or password, please try again.</div>
<?php endif; ?>

<?php echo $form->getRawValue()->renderFormTag($view->router->generate('doctrine_user_session_create'), array('class' => 'doctrine_user_session_new')) ?>
    <div>
        <label for="doctrine_user_session_new_usernameOrEmail">Username or email:</label>
        <?php echo $form['usernameOrEmail']->getRawValue()->render(); ?>
    </div>
    <div>
        <label for="doctrine_user_session_new_password">Password:</label>
        <?php echo $form['password']->getRawValue()->render(); ?>
    </div>
    <div>
        <input type="submit" value="Log in" />
    </div>
</form>
