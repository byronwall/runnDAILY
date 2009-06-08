<h4>{{$message_list.0.msg_subject}}</h4>
<hr class="page_heading">
<div id="message_list">
{{foreach from=$message_list item=message}}
<div id="message_item">
	<p>By: <a href="/community/view_user/{{$message.msg_uid_from}}">{{$message.msg_username_from}}</a> on {{$message.msg_date_created|date_format:"l F j, Y g:i A"}}</p>
	<p>{{$message.msg_message}}</p>
</div>
{{foreachelse}}
	<p>No messages.</p>
{{/foreach}}
</div>
<h5>Reply:</h5>
<form action="/messages/actionReply" method="post">
	<p><textarea rows="7" cols="30" name="msg_message"></textarea></p>
	<input type="hidden" name="msg_convo_id" value="{{$message_list.0.msg_convo_id}}" />
	<input type="hidden" name="msg_uid_to" value="{{if $currentUser->uid == $message_list.0.msg_uid_to}}{{$message_list.0.msg_uid_from}}{{else}}{{$message_list.0.msg_uid_to}}{{/if}}" />
	<input type="hidden" name="msg_subject" value="{{$message_list.0.msg_subject}}" />
	<input type="hidden" name="msg_type" value="1" />
	<p><input type="submit" value="Reply" /></p>
</form>