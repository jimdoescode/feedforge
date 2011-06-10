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

function edit_feed(id, title)
{
	$('#feedtitle').val(title);
	$('#feedid').val(id);
	$('a.fb_link').click();
}

function delete_feed(id)
{
	if(confirm("Are you sure you want to delete this feed?"))$.post(SITE+"admin/delete_feed", {id: id}, refresh_feed_list, 'json');
	return false;
}

function refresh_field_list(response)
{
	console.log(response);
	var tbl = $('table tbody');
	tbl.text('');
	var feed = response.feed;
	var fields = response.fields;
	
	var count = fields.length;
	var rows = '';
	
	for(var i=0; i < count; i++)
		rows += '<tr><td class="center"><a href="#" title="Edit" onclick="return edit_field('+feed['id']+', '+fields[i]['id']+', \''+fields[i]['title']+'\', '+fields[i]['feed_field_type_id']+');">(E)</a>&nbsp;&nbsp;<a href="#" title="Delete" onclick="return delete_field('+feed['id']+', '+fields[i]['id']+');">(X)</a></td><td>'+fields[i]['short']+'</td><td>'+fields[i]['title']+'</td><td>'+fields[i]['type_name']+'</td></tr>';
	if(count == 0)rows += '<tr><td colspan="4">No Fields Found</td></tr>';
	
	tbl.html(rows);
}

function edit_field(feedid, fieldid, title, type)
{
	$('#feedid').val(feedid);
	$('#fieldtitle').val(title);
	$('#fieldid').val(fieldid);
	$('#fieldtype').val(type);
	$('a.fb_link').click();
}

function delete_field(feedid, fieldid)
{
	if(confirm("Are you sure you want to delete this field?"))$.post(SITE+'admin/delete_feed_field/'+feedid, {id: fieldid}, refresh_field_list, 'json');
	return false;
}

function refresh_entry_list(response)
{
	
}

function edit_entry(feedid, entryid)
{
	//use arguments array indexed at 2
}

function delete_entry(feedid, entryid)
{
	
}

function reset_inputs(elmid)
{
	$(elmid+' input[type="text"]').not(':disabled').val('');
	$(elmid+' input[type="hidden"]').not(':disabled').val(0);
	$(elmid+' select').val('');
}

$(document).ready(function() 
{
	var fbl = $('a.fb_link');
	fbl.fancybox(
	{
		'scrolling'	: 'no',
		'titleShow'	: false,
		'showCloseButton' : false,
		'centerOnScroll'  : true,
		'onClosed'	: function() {reset_inputs(fbl.attr('href'));}
	});
	
	$('#modify_feeds').submit(function()
	{
		var title = $.trim($('#feedtitle').val());
		if(title.length > 0)
		{
			var id = $('#feedid').val();
			$.post(SITE+"admin/modify_feeds", {id: id, title: title}, refresh_feed_list, 'json');
			$.fancybox.close();
		}
		else alert("A title is required for a feed.");
		
		return false;
	});
	
	$('#modify_feed_fields').submit(function()
	{
		var title = $.trim($('#fieldtitle').val());
		var type = $.trim($('#fieldtype').val());
		
		if(title.length > 0 && type.length > 0)
		{
			var feedid = $('#feedid').val();
			var fieldid = $('#fieldid').val();
			$.post(SITE+"admin/modify_feed_fields/"+feedid, {id: fieldid, title: title, type: type}, refresh_field_list, 'json');
			$.fancybox.close();
		}
		else if(title.length <= 0)alert("A title is required for a feed field.");
		else alert("A field type is required for a feed field.");
		
		return false;
	});
});