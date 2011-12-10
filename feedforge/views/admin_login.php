<h1><?=lang('admin_login_title');?></h1>
<form id="login" class="green-border" action="<?=site_url('admin/login');?>" method="post">
    <dl class="column">
        <dt><label for="username">User Name <?=form_error('username');?></label></dt>
        <dd><input id="username" name="username" type="text" value="<?=set_value('username');?>"/></dd>
        <dt><label for="password">Password <?=form_error('password');?></label></dt>
        <dd><input id="password" name="password" type="password" value=""/></dd>
        <dt><input type="submit" value="Login"</dt>
    </dl>
    <p class="column"><?=lang('admin_login');?></p>
</form>
