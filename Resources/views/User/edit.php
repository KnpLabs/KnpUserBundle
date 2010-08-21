<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php echo $form->getRawValue()->renderFormTag($view->router->generate('doctrine_user_user_update', array('username' => $username)), array('class' => 'doctrine_user_user_edit')) ?>
    <div>
        <label for="doctrine_user_user_edit_username">Username:</label>
        <?php echo $form['username']->getRawValue()->render(); ?>
        <?php echo $form['username']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <label for="doctrine_user_user_edit_email">Email:</label>
        <?php echo $form['email']->getRawValue()->render(); ?>
        <?php echo $form['email']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <label for="doctrine_user_user_edit_password">Password:</label>
        <?php echo $form['password']->getRawValue()->render(); ?>
        <?php echo $form['password']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <input type="submit" value="Update user" />
    </div>
</form>
