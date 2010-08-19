<?php $view->extend('DoctrineUserBundle::layout') ?>

<div class="doctrine_user_user_list">
    <ul>
    <?php foreach($users as $user): ?>
        <li><a href="<?php echo $view->router->generate('doctrine_user_user_show', array('username' => $user->getUsername())) ?>"><?php echo $user->getUsername()?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
