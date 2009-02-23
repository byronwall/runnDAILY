<div class="grid_12">
	<h2 id="page-heading">Module Management</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/admin/action_new_modules">add new modules</a>
		<a href="/admin/action_hash_modules">recreate module has</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">

<div id="result" style="background-color:green"></div>

<table>
<thead>
	<td>Name</td>
	<td>Code</td>
	<td>Size</td>
</thead>
{{foreach from=$modules item=module}}
<form class="page_item" action="/admin/update_page" method="post" id="form_{{counter}}">
	<input type="hidden" name="m_name" value="{{$module->name}}" />
	<tr class="{{cycle values=" , alt_row"}}">
		<td><input type="text" value="{{$module->name}}" name="m_name" class="required"/></td>
		<td><input type="text" value="{{$module->code}}" name="m_code" class="required"/></td>
		<td><input type="text" value="{{$module->size}}" name="m_size" /></td>
		<td><input type="submit" value="update" /></td>
	</tr>
</form>
{{foreachelse}}
No stats found!
{{/foreach}}
</table>
</div>
{{include file="modules/hash.tpl"}}
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