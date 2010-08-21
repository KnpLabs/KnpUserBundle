<?php $view->extend('DoctrineUserBundle::layout') ?>

<div class="doctrine_user_permission_list">
    <ul>
    <?php foreach($permissions as $permission): ?>
        <li><a href="<?php echo $view->router->generate('doctrine_user_permission_show', array('name' => $permission->getName())) ?>"><?php echo $permission->getName()?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
