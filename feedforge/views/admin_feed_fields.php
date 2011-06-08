<h1>Fields for <?=$feed['title'];?></h1>
<form class="table-border blue-border" action="#">
    <table>
        <thead>
            <tr>
                <th class="medium"></th>
                <th class="large">Tag Name</th>
                <th class="large">Field Title</th>
                <th class="large">Field Type</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($fields as $field): ?>
            <tr><td class='center'><a href='#' title='Edit'>(E)</a>&nbsp;&nbsp;<a href='#' title='Delete'>(X)</a></td><td><?=$field['short'];?></td><td><?=$field['title'];?></td><td><?=$field['type_name'];?></td></tr>
<?php
    endforeach;
    if(empty($fields)):
?>
            <tr><td colspan='5'>No Fields Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</form>
<a class="fb_link" href="#modify_feed_fields">Add New Field &raquo;</a>
<div style="display: none">
    <form id="modify_feed_fields" action="">
        <p>
			<label for="fieldtitle">Field Title: </label>
			<input type="text" id="fieldtitle" name="fieldtitle" size="30"/>
        </p>
        <p>
            <label for="fieldtype">Field Type: </label>
			<select id="fieldtype" name="fieldtype">
                <option value="" selected="true"></option>
<?php foreach($types as $type): ?>
                <option value="<?=$type['id'];?>"><?=$type['title'];?></option>
<?php endforeach; ?>
            </select>
            <input type="hidden" id="fieldid" name="fieldid" value="0"/>
		</p>
        <p style="text-align: right;"><input type="button" value="Cancel" onclick="$.fancybox.close();"/> <input type="submit" value="Submit"/></p>
    </form>
</div>