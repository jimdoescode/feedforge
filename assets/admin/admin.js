function refresh_feed_list(response)
{
	var tbl = $('table tbody');
	tbl.text('');
	var feeds = response.feeds;
	
	var count = feeds.length;
	var rows = '';
	
	for(var i=0; i < count; i++)
		rows += '<tr><td class="center"><a href="#" title="Edit" onclick="return edit_feed('+feeds[i]['id']+', \''+feeds[i]['title']+'\')">(E)</a>&nbsp;&nbsp;<a href="'+SITE+'admin/delete_feed/'+feeds[i]['id']+'" title="Delete" onclick="return delete_feed('+feeds[i]['id']+');">(X)</a></td><td class="center">'+feeds[i]['id']+'</td><td>'+feeds[i]['title']+'</td><td>'+feeds[i]['short']+'</td><td><a href="'+SITE+'admin/feed_fields/'+feeds[i]['id']+'" title="Fields">View Fields &raquo;</a><br/><a class="green-text" href="'+SITE+'admin/feed_entries/'+feeds[i]['id']+'">View Entries &raquo;</a></td></tr>';
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
	var tbl = $('table tbody');
	tbl.text('');
	var feed = response.feed;
	var fields = response.fields;
	
	var count = fields.length;
	var rows = '';
	
	for(var i=0; i < count; i++)
		rows += '<tr><td class="center"><a href="#" title="Edit" onclick="return edit_field('+feed['id']+', '+fields[i]['id']+', \''+fields[i]['title']+'\', '+fields[i]['feed_field_type_id']+');">(E)</a>&nbsp;&nbsp;<a href="#" title="Delete" onclick="return delete_field('+feed['id']+', '+fields[i]['id']+');">(X)</a></td><td>'+fields[i]['title']+'</td><td>'+fields[i]['short']+'</td><td>'+fields[i]['type_name']+'</td></tr>';
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
	var tbl = $('table tbody');
	tbl.text('');
	var feed = response.feed;
	var fields = response.fields;
	var entries = response.entries;
	
	var fieldcount = fields.length;
	var entrycount = entries.length;
	var rows = '';
	
	for(var i=0; i < entrycount; i++)
	{
		rows += '<tr>';
		rows += '<td class="center"><a href="#" class="green-text" title="Edit" onclick="return edit_entry('+feed.id+', '+entries[i]['id']+', this);">(E)</a>&nbsp;&nbsp;<a href="#" class="green-text" title="Delete" onclick="return delete_entry('+feed.id+', '+entries[i]['id']+')">(X)</a></td>';
        rows += '<td class="center">'+entries[i]['id']+'</td>';
        for(var j=0; j < fieldcount; j++)
        {
        	var val = entries[i][fields[j]['short']];
        	val = (val == null) ? '' : val;
        	rows += '<td class="entry_column" title="'+fields[j]['short']+'">'+val+'</td>';
        }
		rows += '</tr>';
	}
	if(entrycount == 0)rows += '<tr><td colspan="'+fieldcount+'">No Entries Found.</td></tr>';
	
	tbl.html(rows);
}

function edit_entry(feedid, entryid, elm)
{
	$('#feedid').val(feedid);
	$('#entryid').val(entryid);
	
	$(elm).parent().siblings('td.entry_column').each(function()
	{
		var id = $(this).attr('title');
		var val = $(this).html();
		$('#'+id).val(val);
	});
	
	$('a.fb_link').click();
	return false;
}

function delete_entry(feedid, entryid)
{
	if(confirm("Are you sure you want to delete this entry?"))$.post(SITE+'admin/delete_feed_entry/'+feedid, {id: entryid}, refresh_entry_list, 'json');
	return false;
}

function refresh_variable_list(response)
{
	var tbl = $('table tbody');
	tbl.text('');
	var vars = response.variables;
	
	var count = vars.length;
	var rows = '';
	
	for(var i=0; i < count; i++)
		rows += '<tr><td class="center"><a href="#" class="purple-text" title="Edit" onclick="return edit_variable('+vars[i]['id']+', \''+vars[i]['title']+'\', \''+vars[i]['value']+'\')">(E)</a>&nbsp;&nbsp;<a href="'+SITE+'admin/delete_variable/'+vars[i]['id']+'" class="purple-text" title="Delete" onclick="return delete_variable('+vars[i]['id']+');">(X)</a></td><td class="center">'+vars[i]['id']+'</td><td>'+vars[i]['title']+'</td><td>'+vars[i]['short']+'</td><td>'+vars[i]['value']+'</td></tr>';
	if(count == 0)rows += '<tr><td colspan="5">No Variables Found</td></tr>';
	
	tbl.html(rows);
}

function edit_variable(varid, title, value)
{
	$('#variableid').val(varid);
	$('#variabletitle').val(title);
	$('#variablevalue').val(value);
	$('a.fb_link').click();	
}

function delete_variable(varid)
{
	if(confirm("Are you sure you want to delete this variable?"))$.post(SITE+'admin/delete_variable', {id: varid}, refresh_variable_list, 'json');
	return false;
}

//Will need to add input types to this as they 
//arrive. Should be good for now though.
function reset_inputs(elmid)
{
	var today = new Date();
	var date = (today.getMonth()+1)+'/'+today.getDate()+'/'+today.getFullYear();
	$(elmid+' input[type="date"]').not(':disabled').val(date);
	$(elmid+' input[type="text"]').not(':disabled').val('');
	$(elmid+' input[type="password"]').not(':disabled').val('');
	$(elmid+' input[type="hidden"]').not(':disabled').val(0);
	$(elmid+' select').val('');
	$(elmid+' textarea').val('');
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
	
	$('#modify_feed_entries').submit(function()
	{
		var feedid = $.trim($('#feedid').val());
		var entryid = $.trim($('#entryid').val());
		
		var data = {id: entryid};
		
		//Use the field labels to get the input id
		$('label.entry_label').each(function()
		{
			var id = $(this).attr('for');
			data[id] = $.trim($('#'+id).val());
		});

		$.post(SITE+"admin/modify_feed_entries/"+feedid, data, refresh_entry_list, 'json');
		$.fancybox.close();
		return false;
	});
	
	$('#modify_variables').submit(function()
	{
		var title = $.trim($('#variabletitle').val());
		var value = $.trim($('#variablevalue').val());
		
		if(title.length > 0 && value.length > 0)
		{
			var varid = $.trim($('#variableid').val());
			$.post(SITE+"admin/modify_variables/", {id: varid, title: title, value: value}, refresh_variable_list, 'json');
			$.fancybox.close();
		}	
		else if(title.length <= 0)alert("A title is required for a variable.");
		else alert("A value is required for a variable.");
		
		return false;
	});
});