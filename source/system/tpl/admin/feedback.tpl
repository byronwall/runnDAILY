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
	<thead><td>Date</td><td>User</td><td colspan="2">Feedback</td></thead>
	<tbody>
	{{foreach from=$message_list item=message}}
		<tr class="feedback_item">
			<td>{{$message.msg_date_created|date_format}}</td>
			<td>{{if $message.msg_uid_from}}<a href="/community/view_user/{{$message.msg_uid_from}}" class="icon"><img src="/img/icon/user_friend.png" />{{$message.u_username}}</a>{{else}}Guest{{/if}}</td>
			<td>{{$message.msg_message}}</td>
			<td><a href="#delete_modal" class="form icon" rel="{{$message.msg_convo_id}}"><img src="/img/icon/delete.png" /></a></td>
		</tr>
	{{/foreach}}
	</tbody>
</table>

<div id="delete_modal" style="display:none">
	<form action="/feedback/delete" method="post" id="form_delete">
		<h4>Are you sure you want to delete that message?</h4>
		<p class="alert_red">Deleteing feedback cannot be undone! Make sure you no longer need the feedback item before deleting it!</p>
		<input type="hidden" name="msg_convo_id" value="-1" />
		<p>
			<input type="submit" value="Delete" />
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</p>
	</form>
</div>
</div>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(	function(){
	$("a.form").click(function(){
		$.debug(this);
		$("#form_delete [name=msg_convo_id]").val(this.rel);
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