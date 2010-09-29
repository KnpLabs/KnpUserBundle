<?php $view->extend('DoctrineUserBundle::layout.php') ?>

<div class="doctrine_user_group_list">
    <ul>
    <?php foreach ($groups as $group): ?>
        <li><a href="<?php echo $view['router']->generate('doctrine_user_group_show', array('name' => $group->getName())) ?>"><?php echo $group->getName()?></a></li>
    <?php endforeach; ?>
    </ul>
</div>
