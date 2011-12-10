<h1>Step 2 of 2: Database Information</h1>
<form id="login" class="green-border" action="<?=site_url('installer/step/2')?>" method="POST">
    <dl class="column">
        <dt><label for="host">Database Host <?=form_error('host');?></label></dt>
        <dd><input id="host" name="host" type="text" value="<?=set_value('host', 'localhost');?>"/></dd>

        <dt><label for="database">Database Name <?=form_error('database');?></label></dt>
        <dd><input id="database" name="database" type="text" value="<?=set_value('database');?>"/></dd>

        <dt><label for="username">Database User <?=form_error('username');?></label></dt>
        <dd><input id="username" name="username" type="text" value="<?=set_value('username');?>"/></dd>

        <dt><label for="password">Database Password <?=form_error('password');?></label></dt>
        <dd><input id="password" name="password" type="password" value="<?=set_value('password');?>"/></dd>

        <dd><input id="submit" value="submit" type="submit"/></dd>
    </dl>
    <p class="column"><?=lang('database_information');?></p>
</form>