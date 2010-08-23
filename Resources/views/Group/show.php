<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view['session']->hasFlash('doctrine_user_group_create/success')): ?>
<div class="doctrine_user_user_create_success">The group has been created successfully</div>
<?php elseif ($view['session']->hasFlash('doctrine_user_group_update/success')): ?>
<div class="doctrine_user_user_update_success">The group has been updated successfully</div>
<?php endif; ?>


<div class="doctrine_user_group_show">
    <table>
        <tbody>
            <tr>
                <th>Name</th>
                <td><?php echo $group->getName() ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo nl2br($group->getDescription()) ?></td>
            </tr>
        </tbody>
    </table>
</div>