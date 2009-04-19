<div class="grid_12">
	<h2 id="page-heading">User Feedback</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
<div id="result"></div>

<table>
	<thead><td>date</td><td>user</td><td>message</td></thead>
	{{foreach from=$message item=message}}
		<tr class="feedback_item">
			<td>{{$message->date|date_format}}</td>
			<td>{{$message->user->username|default:anon}}</td>
			<td>{{$message->msg}}</td>
			<td><a href="#delete_modal" class="form" rel="{{$message->mid}}">delete</a></td>
		</tr>
	{{foreachelse}}
	No feedback!
	{{/foreach}}
</table>

<div id="delete_modal" style="display:none">
	<form action="/feedback/delete" method="post" id="form_delete">
		<h1>Are you sure you want to delete that message?</h1>
		<input type="hidden" name="m_mid" value="-1" />		
		<input type="submit" value="yes, delete" />
		<input type="button" value="cancel" onclick="$.facebox.close()" />
	</form>
</div>
</div>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(	function(){
	$("a.form").click(function(){
		$.debug(this);
		$("#form_delete [name=m_mid]").val(this.rel);
		$("#form_delete").data("row", $(this).parent().parent());
	});
	$("a.form").click($.facebox.clickHandler);
	
	$("#form_delete").validate({
		submitHandler: function(form){
			$(form).ajaxSubmit({
				success: function(data){
					if(data){
						$("#form_delete").data("row").remove();
						$("#results").text("deleted");
					}
				}
			});
			$.facebox.close();
		}
	});
});

</script>