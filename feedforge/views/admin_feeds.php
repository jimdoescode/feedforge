<h1>Feeds</h1>
<div class="table-border blue-border">
    <table>
        <thead>
            <tr>
                <th class="small"></th>
                <th class="medium center">Feed ID</th>
                <th class="large">Feed Title</th>
                <th class="large">Feed Tag Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
<?php foreach($feeds as $feed): ?>
            <tr><td class="center"><a href='<?=site_url('admin/feed_fields/'.$feed['id']);?>' title='Fields'>(F)</a>&nbsp;&nbsp;<a href='#' title='Edit' onclick='return edit_feed(<?=$feed['id'];?>, "<?=$feed['title'];?>");'>(E)</a>&nbsp;&nbsp;<a href='<?=site_url('admin/delete_feed/'.$feed['id']);?>' title='Delete' onclick='return delete_feed(<?=$feed['id'];?>);'>(X)</a></td><td class="center"><?=$feed['id'];?></td><td><?=$feed['title'];?></td><td><?=$feed['short'];?></td><td><a href="<?=site_url('admin/feed_entries/'.$feed['id']);?>">View Entries &raquo;</a></td></tr>
<?php
    endforeach;
    if(empty($feeds)):
?>
            <tr><td colspan='5'>No Feeds Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</div>
<a class="fb_link" href="#modify_feeds">Add New Feed &raquo;</a>
<div style="display: none">
    <form id="modify_feeds" class="fancy-form" action="">
        <p>
			<label for="feedtitle">Feed Title: </label>
			<input type="text" id="feedtitle" name="feedtitle" size="30"/>
            <input type="hidden" id="feedid" name="feedid" value="0"/>
		</p>
        <p style="text-align: right;"><input type="button" value="Cancel" onclick="$.fancybox.close();"/> <input type="submit" value="Submit"/></p>
    </form>
</div>