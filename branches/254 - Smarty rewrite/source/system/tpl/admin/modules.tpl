<div class="grid_12">
	<h2 id="page-heading">Module Management</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/admin/action_new_modules">add new modules</a>
		<a href="/admin/action_hash_modules">recreate module hash</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">

<div id="result" style="background-color:green"></div>

<table>
<thead>
	<td>Code</td>
	<td>Name</td>
	<td>Title</td>
	<td>Description</td>
	<td>Location</td>
</thead>
<tbody>
{{foreach from=$modules item=module}}
	<tr class='{{cycle values=" ,odd"}}'>
		<form class="page_item" action="/admin/update_module" method="post" id="form_{{counter}}">
			<input type="hidden" name="m_code" value="{{$module->code}}" />
			<td>{{$module->code}}</td>
			<td>{{$module->name}}</td>
			<td><input type="text" value="{{$module->title}}" name="m_title"></td>
			<td><input type="text" value="{{$module->desc}}" name="m_desc"></td>
			<td>
			{{htmlOptions name="m_loc" output=$module_types values=$module_types selected=$module->loc}}
			</td>
			<td><input type="submit" value="save"></td>
		</form>
	</tr>
{{foreachelse}}
No stats found!
{{/foreach}}
</tbody>
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