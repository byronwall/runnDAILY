<h4>{{$message_list.0.msg_subject}}</h4>
{{foreach from=$message_list item=message}}
	<p>By: {{$message.msg_username_from}} on {{$message.msg_date_created|date_format:"l F j, Y g:i A"}}</p>
	<p>{{$message.msg_message}}</p>
	<hr>
{{foreachelse}}
	<p>No messages.</p>
{{/foreach}}
<p>Reply:</p>
<form>
<p><textarea rows="7" cols="30"></textarea></p>
<p><input type="submit" value="Reply" /></p>
</form>