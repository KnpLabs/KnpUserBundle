<?php $view->extend('DoctrineUserBundle::layout.php') ?>

<?php if ($view['session']->hasFlash('doctrine_user_session_create/error')): ?>
<div class="doctrine_user_session_create_error">Bad username or password, please try again.</div>
<?php endif; ?>

<?php echo $form->form($view['router']->generate('doctrine_user_session_create'), array('class' => 'doctrine_user_session_new')) ?>
    <div>
        <?php echo $form['usernameOrEmail']->label('Username or email:') ?>
        <?php echo $form['usernameOrEmail']->widget(); ?>
    </div>
    <div>
        <?php echo $form['password']->label('Password:') ?>
        <?php echo $form['password']->widget() ?>
    </div>
    <div>
        <?php echo $form['rememberMe']->label('Remember me:') ?>
        <?php echo $form['rememberMe']->widget() ?>
    </div>
    <div>
        <input type="submit" value="Log in" />
    </div>
</form>
