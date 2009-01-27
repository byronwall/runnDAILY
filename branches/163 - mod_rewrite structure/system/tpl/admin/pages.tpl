<h1>site pages</h1>

<div id="result" style="background-color:green"></div>

<table>
<thead>
	<td>address</td><td>permissions</td><td>title</td><td>tab</td><td>common</td>
</thead>
{{foreach from=$pages item=p_page}}
<form class="page_item" action="/lib/action_page.php" method="post" id="form_{{counter}}">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="p_page_name" value="{{$p_page->page_name}}" />
	<tr class="{{cycle values=" , alt_row"}}">
		<td>{{$p_page->page_name}}</td>
		<td><input type="text" value="{{$p_page->min_permission}}" name="p_min_permission" class="number required"/></td>
		<td><input type="text" value="{{$p_page->title}}" name="p_title" class="required"/></td>
		<td><input type="text" value="{{$p_page->tab}}" name="p_tab" /></td>
		<td><input type="text" value="{{$p_page->common}}" name="p_common" /></td>
		<td><input type="submit" value="update" /></td>
	</tr>
</form>
{{foreachelse}}
No stats found!
{{/foreach}}
</table>

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