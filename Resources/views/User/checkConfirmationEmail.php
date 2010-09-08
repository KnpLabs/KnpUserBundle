An email has been sent to <?php echo $user->getEmail() ?>. It contains an activation link you must click to activate your account.

<?php if($debug): ?>
<pre><?php $view['actions']->output('DoctrineUserBundle:User:renderConfirmationEmail', array('email' => $user->getEmail())); ?></pre>
<?php endif; ?>
