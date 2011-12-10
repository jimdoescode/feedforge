<h1>Step 1 of 2: Site Information</h1>
<form id="login" class="blue-border" action="<?=site_url('installer/step/1')?>" method="POST">
    <dl class="column">
        <dt><label for="baseurl">Base URL with trailing slash <?=form_error('baseurl');?></label></dt>
        <dd><input id="baseurl" name="baseurl" type="text" value="<?=set_value('baseurl');?>"/></dd>

        <dt><label for="username">Admin User Name <?=form_error('username');?></label></dt>
        <dd><input id="username" name="username" type="text" value="<?=set_value('username');?>"/></dd>

        <dt><label for="password">Admin Password <?=form_error('password');?></label></dt>
        <dd><input id="password" name="password" type="password" value="<?=set_value('password');?>"/></dd>

        <dd><input id="submit" value="submit" type="submit"/></dd>
    </dl>
    <p class="column"><?=lang('site_information');?></p>
</form>