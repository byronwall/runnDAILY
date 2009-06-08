<h4 class="float_left">{{$message_list.0.msg_subject}}</h4>
<a href="/messages/delete/{{$message_list.0.msg_convo_id}}" class="float_right facebox icon"><img src="/img/icon/delete.png" />Delete Conversation</a>
<hr class="page_heading">
<div id="message_list">
{{foreach from=$message_list item=message}}
<div id="message_item">
	<p>By: <a href="/community/view_user/{{$message.msg_uid_from}}">{{$message.msg_username_from}}</a> on {{$message.msg_date_created|date_format:"l F j, Y g:i A"}}</p>
	<p class="{{if $message.msg_uid_to == $currentUser->uid && $message.msg_new}}bold {{/if}}message">{{$message.msg_message}}</p>
</div>
{{foreachelse}}
	<p>No messages. There was probably an error during the retrieval process. Please submit your request again.</p>
{{/foreach}}
</div>
<p><h5>Reply:</h5><p>
<form id="reply_form" action="/messages/actionReply" method="post">
	<p><textarea rows="3" cols="40" name="msg_message"></textarea></p>
	<input type="hidden" name="msg_convo_id" value="{{$message_list.0.msg_convo_id}}" />
	<input type="hidden" name="msg_uid_to" value="{{if $currentUser->uid == $message_list.0.msg_uid_to}}{{$message_list.0.msg_uid_from}}{{else}}{{$message_list.0.msg_uid_to}}{{/if}}" />
	<input type="hidden" name="msg_subject" value="{{$message_list.0.msg_subject}}" />
	<input type="hidden" name="msg_type" value="1" />
	<div id="reply_error_box"></div>
	<p>
		<input type="submit" value="Reply" />
		<input type="button" value="Cancel" onclick="$.facebox.close()" />
	</p>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$("#reply_form").validate({
		onkeyup: false,
		onclick: false,
		onfocusout: false,
		rules: {
			msg_message: {
				required: true
			}
		},
		messages: {
			msg_message: {
				required: "Please enter a reply message before submitting."
			}
		},
		errorLabelContainer: "#reply_error_box",
		errorElement: "p",
		//errorClass: "alert_red",
		submitHandler : function(form){
			$(form).ajaxSubmit({
				success: function(data){
					$(form).clearForm();
					$.facebox("Reply sent.", 1000);
				}
			});
		}
	});
});
</script>