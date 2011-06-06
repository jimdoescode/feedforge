<h1>Feeds</h1>
<form class="table-border blue-border" action="#">
    <table>
        <thead>
            <tr>
                <th class="small"></th>
                <th class="medium center">Feed ID</th>
                <th class="large">Feed Title</th>
                <th class="large">Feed Short Title</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
<?php foreach($feeds as $feed): ?>
            <tr><td class="center"><a href='<?=site_url('admin/feed_fields/'.$feed['id']);?>' title='Fields'>(F)</a>&nbsp;&nbsp;<a href='#' title='Edit' onclick='return edit_feed(<?=$feed['id'];?>, "<?=$feed['title'];?>");'>(E)</a>&nbsp;&nbsp;<a href='<?=site_url('admin/delete_feed/'.$feed['id']);?>' title='Delete' onclick='return delete_feed(<?=$feed['id'];?>);'>(X)</a></td><td class="center"><?=$feed['id'];?></td><td><?=$feed['title'];?></td><td><?=$feed['short'];?></td><td><a href="#">View Entries &raquo;</a></td></tr>
<?php
    endforeach;
    if(empty($feeds)):
?>
            <tr><td colspan='5'>No Feeds Found.</td></tr>
<?php endif; ?>
        </tbody>
    </table>
</form>
<a href="#" class="blue-text" onclick="return add_feed();">Add New Feed &raquo;</a>