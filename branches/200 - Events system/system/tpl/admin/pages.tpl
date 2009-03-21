<div class="grid_12">
	<h2 id="page-heading">Page Management</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/admin/action_new_pages">add new pages</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">

<div id="result" style="background-color:green"></div>

<table>
<thead>
	<td>Address</td><td>Permissions</td><td>Title</td><td>Tab</td><td>Common</td>
</thead>
<tbody>
{{foreach from=$pages item=p_page}}
<form class="page_item" action="/admin/update_page" method="post" id="form_{{counter}}">
	<input type="hidden" name="p_page_name" value="{{$p_page->page_name}}" />
	<tr class='{{cycle values=" , odd"}}'>
		<td>{{$p_page->page_name}}</td>
		<td>
		{{html_options name="p_perm_code" output=$page_perms values=$page_perms selected=$p_page->perm_code}}
		</td>
		<td><input type="text" value="{{$p_page->title}}" name="p_title" class="required"/></td>
		<td><input type="text" value="{{$p_page->tab}}" name="p_tab" /></td>
		<td><input type="text" value="{{$p_page->common}}" name="p_common" /></td>
		<td><input type="submit" value="update" /></td>
	</tr>
</form>
{{foreachelse}}
<tr>
	<td>No stats found!</td>
</tr>
{{/foreach}}
<tbody>
</table>
</div>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(	function(){
	$("form.page_item").each(function(){
		$(this).validate({
			submitHandler: function(form){
				$(form).ajaxSubmit({
					target: "#result"
				});
			}
		});
	});
});

</script>