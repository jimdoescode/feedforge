<h1>Variables</h1>
<div class="table-border purple-border">
    <table>
        <thead>
            <tr>
                <th class="small"></th>
                <th class="small center">ID</th>
                <th class="large">Title</th>
                <th class="large">Tag Name</th>
                <th class="large">Value</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($variables as $var): ?>
            <tr><td class="center"><a href='#' title='Edit' class="purple-text" onclick='return edit_variable(<?=$var['id'];?>, "<?=$var['title'];?>", "<?=$var['value'];?>");'>(E)</a>&nbsp;&nbsp;<a href='<?=site_url('admin/delete_variable/'.$var['id']);?>' class='purple-text' title='Delete' onclick='return delete_variable(<?=$var['id'];?>);'>(X)</a></td><td class="center"><?=$var['id'];?></td><td><?=$var['title'];?></td><td><?=$var['short'];?></td><td><?=$var['value'];?></td></tr>
<?php
    endforeach;
    if(empty($variables)):
?>
            <tr><td colspan='5'>No Variables Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</div>
<a class="fb_link purple-text" href="#modify_variables">Add New Variable &raquo;</a>
<div style="display: none">
    <form id="modify_variables" class="fancy-form" action="">
        <p>
			<label for="variabletitle">Variable Title: </label>
			<input type="text" id="variabletitle" name="variabletitle" size="30"/>
            <input type="hidden" id="variableid" name="variableid" value="0"/><br/>
            <label for="variablevalue">Variable Value: </label>
            <textarea id="variablevalue" name="variablevalue"></textarea>
		</p>
        <p style="text-align: right;"><input type="button" value="Cancel" onclick="$.fancybox.close();"/> <input type="submit" value="Submit"/></p>
    </form>
</div>