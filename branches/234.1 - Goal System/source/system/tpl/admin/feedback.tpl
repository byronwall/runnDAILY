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
	{{foreach from=$message item=message}}
		<tr class="feedback_item">
			<td>{{$message->date|date_format}}</td>
			<td>{{$message->user->username|default:anon}}</td>
			<td>{{$message->msg}}</td>
			<td><a href="#delete_modal" class="form icon" rel="{{$message->mid}}"><img src="/img/icon/delete.png" /></a></td>
		</tr>
	{{foreachelse}}
		<tr>No feedback!</tr>
	{{/foreach}}
	</tbody>
</table>

<div id="delete_modal" style="display:none">
	<form action="/feedback/delete" method="post" id="form_delete">
		<h4>Are you sure you want to delete that message?</h4>
		<p class="alert_red">Deleteing feedback cannot be undone! Make sure you no longer need the feedback item before deleting it!</p>
		<input type="hidden" name="m_mid" value="-1" />
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