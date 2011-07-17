<?php if($first_time): ?>
<h1>Please Create an Admin Account</h1>
<?php else: ?>
<h1>Please Login</h1>
<?php endif; ?>
<form id="login" class="green-border" action="<?=site_url('admin/login');?>" method="post">
    <dl class="column">
        <dt><label for="username">User Name <?=form_error('username');?></label></dt>
        <dd><input id="username" name="username" type="text" value="<?=set_value('username');?>"/></dd>
        <dt><label for="password">Password <?=form_error('password');?></label></dt>
        <dd><input id="password" name="password" type="password" value=""/></dd>
        <dt><input type="submit" value="<?=($first_time ? 'Create' : 'Login');?>"</dt>
    </dl>
    <p class="column"><?=($first_time ? lang('admin_create') : lang('admin_login'));?></p>
</form>
