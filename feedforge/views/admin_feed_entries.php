<h1>Entries for <?=$feed['title'];?></h1>
<form class="table-border purple-border" action="#">
    <table>
        <thead>
            <tr>
                <th class="medium"></th>
                <th class="medium center">Entry ID</th>
<?php foreach($fields as $field): ?>
                <th><?=$field['short'];?></th>
<?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
<?php
    $entryCount = count($entries);
    $fieldCount = count($fields);
    for($i=0; $i < $entryCount; $i++):
?>
            <tr>
                <td class='center'><a href='#' class="purple-text" title='Edit' onclick='return edit_feed_entry(<?=$feed['id'];?>, <?=$entries[$i]['id'];?>)'>(E)</a>&nbsp;&nbsp;<a href='#' class="purple-text" title='Delete' onclick="return delete_entry(<?=$feed['id'];?>, <?=$entries[$i]['id'];?>)">(X)</a></td>
                <td class="center"><?=$entries[$i]['id'];?></td>
<?php   for($j=0; $j < $fieldCount; $j++):?>
                <td><?=$entries[$i][$fields[$j]['short']];?></td> 
<?php   endfor;?>
            </tr>
<?php 
    endfor;
    if($entryCount == 0):
?>
            <tr><td colspan='<?=$fieldCount;?>'>No Fields Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</form>