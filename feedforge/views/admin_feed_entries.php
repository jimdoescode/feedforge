<?php
$entryCount = count($entries);
$fieldCount = count($fields);    
?>
<h1>Entries for <?=$feed['title'];?></h1>
<form class='table-border green-border' action='#'>
    <table>
        <thead>
            <tr>
                <th class='medium'></th>
                <th class='medium center'>Entry ID</th>
<?php for($i=0; $i < $fieldCount; $i++): ?>
                <th><?=$fields[$i]['title'];?></th>
<?php endfor; ?>
            </tr>
        </thead>
        <tbody>
<?php for($i=0; $i < $entryCount; $i++): ?>
            <tr>
                <td class='center'><a href='#' class='green-text' title='Edit' onclick='return edit_entry(<?=$feed['id'];?>, <?=$entries[$i]['id'];?>, this);'>(E)</a>&nbsp;&nbsp;<a href='#' class='green-text' title='Delete' onclick='return delete_entry(<?=$feed['id'];?>, <?=$entries[$i]['id'];?>);'>(X)</a></td>
                <td class='center'><?=$entries[$i]['id'];?></td>
<?php   for($j=0; $j < $fieldCount; $j++):?>
                <td class='entry_column' title='<?=$fields[$j]['short'];?>'><?=$entries[$i][$fields[$j]['short']];?></td> 
<?php   endfor;?>
            </tr>
<?php 
    endfor;
    if($entryCount == 0):
?>
            <tr><td colspan='<?=$fieldCount;?>'>No Entries Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</form>
<a class='fb_link green-text' href='#modify_feed_entries'>Add New Entry &raquo;</a>
<div style='display: none;'>
    <form id='modify_feed_entries' class='fancy-form' action=''>
        <input type='hidden' id='feedid' name='feedid' disabled='true' value='<?=$feed['id'];?>'/>
        <input type='hidden' id='entryid' name='entryid' disabled='true' value='0'/>
<?php for($i=0; $i < $fieldCount; $i++): ?>
        <p>
			<label class='entry_label' for='<?=$fields[$i]['short'];?>'><?=$fields[$i]['title'];?>: </label>
			<?=$fields[$i]['input'];?>
            
        </p>
<?php endfor; ?>
        <p style='text-align: right;'><input type='button' value='Cancel' onclick='$.fancybox.close();'/> <input type='submit' value='Submit'/></p>
    </form>
</div>