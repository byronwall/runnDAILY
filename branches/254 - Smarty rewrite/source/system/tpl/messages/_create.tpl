<h4>New Message</h4>
<form action="/messages/actionCreate" method="post">
	<p>To:</p>
	<p>{{htmlOptions name=msg_uid_to options=$friend_list selected=$uid_to}}</p>
	<p>Subject:</p>
	<p><input type="text" name="msg_subject" /></p>
	<p>Message:</p>
	<p><textarea rows="7" cols="30" name="msg_message"></textarea></p>
	<input type="hidden" name="msg_type" value="1"/>
	<p>
		<input type="submit" value="Send">
		<input type="button" value="Cancel" onclick="$.facebox.close()" />
	</p>
</form>