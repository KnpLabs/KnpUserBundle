<?php $view->extend('DoctrineUserBundle::layout') ?>

<?php if ($view['session']->hasFlash('doctrine_user_permission_create/success')): ?>
<div class="doctrine_user_permission_create_success">The permission has been created successfully</div>
<?php elseif ($view['session']->hasFlash('doctrine_user_permission_update/success')): ?>
<div class="doctrine_user_permission_update_success">The permission has been updated successfully</div>
<?php endif; ?>


<div class="doctrine_user_permission_show">
    <table>
        <tbody>
            <tr>
                <th>Name</th>
                <td><?php echo $permission->getName() ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo nl2br($permission->getDescription()) ?></td>
            </tr>
        </tbody>
    </table>
</div>