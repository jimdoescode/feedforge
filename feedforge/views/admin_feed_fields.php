<h1>Fields for <?=$name;?></h1>
<form class="table-border blue-border" action="#">
    <table>
        <thead>
            <tr>
                <th class="small"></th>
                <th class="medium">Tag Name</th>
                <th class="large">Field Title</th>
                <th class="large">Field Type</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
<?php foreach($fields as $field): ?>
            <tr><td class='center'><input type='checkbox' name=''/></td><td><?=$field['short'];?></td><td><?=$field['title'];?></td><td><?=$field['type_name'];?></td><td><a href="#" class="blue-text">Edit Fields &raquo;</a></td></tr>
<?php endforeach; ?>
            <tr><td colspan='5'><?=empty($fields) ? 'No Fields Found.' : '<input type="submit" value="Delete"/>';?></td></tr>
        </tbody>
    </table>
</form>
