function refresh_feed_list(response)
{
	var tbl = $('table tbody');
	tbl.text('');
	var feeds = response.feeds;
	
	var count = feeds.length;
	var rows = '';
	
	for(var i=0; i < count; i++)
		rows += '<tr><td class="center"><a href="'+SITE+'admin/feed_fields/'+feeds[i]['id']+'" title="Fields">(F)</a>&nbsp;&nbsp;<a href="#" title="Edit" onclick="return edit_feed('+feeds[i]['id']+', \''+feeds[i]['title']+'\')">(E)</a>&nbsp;&nbsp;<a href="'+SITE+'admin/delete_feed/'+feeds[i]['id']+'" title="Delete" onclick="return delete_feed('+feeds[i]['id']+');">(X)</a></td><td class="center">'+feeds[i]['id']+'</td><td>'+feeds[i]['title']+'</td><td>'+feeds[i]['short']+'</td><td><a href="#">View Entries &raquo;</a></td></tr>';
	if(count == 0)rows += '<tr><td colspan="5">No Feeds Found</td></tr>';
	
	tbl.html(rows);
}

function add_feed()
{
	var title = prompt("Please enter the title of the new feed.");
	if(title && title != '')$.post(SITE+"admin/add_feed", {title: title}, refresh_feed_list, 'json');
	return false;
}

function edit_feed(id, title)
{
	title = prompt("Please enter the new title for this feed.", title);
	if(title && title != '')$.post(SITE+"admin/update_feed", {id: id, title: title}, refresh_feed_list, 'json');
	return false;
}

function delete_feed(id)
{
	if(confirm("Are you sure you want to delete this feed?"))$.post(SITE+"admin/delete_feed", {id: id}, refresh_feed_list, 'json');
	return false;
}