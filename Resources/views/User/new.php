<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php echo $form->getRawValue()->renderFormTag($view->router->generate('doctrine_user_user_create'), array('class' => 'doctrine_user_user_new')) ?>
    <div>
        <label for="<?php $form['username']->getId() ?>">Username:</label>
        <?php echo $form['username']->getRawValue()->render(); ?>
        <?php echo $form['username']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <label for="<?php echo $form['email']->getId() ?>">Email:</label>
        <?php echo $form['email']->getRawValue()->render(); ?>
        <?php echo $form['email']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <label for="<?php echo $form['password']->getId() ?>">Password:</label>
        <?php echo $form['password']->getRawValue()->render(); ?>
        <?php echo $form['password']->getRawValue()->renderErrors() ?>
    </div>
    <div>
        <input type="submit" value="Create user" />
    </div>
</form>
