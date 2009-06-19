<h4>Are you sure you want to delete this conversation?</h4>
<hr class="page_heading">
<p class="alert_red">Once a conversation has been deleted, it cannot be recovered. Make sure you no longer need a conversation before deleting it.</p>
<p>Deleting a conversation does not prevent you from having futur correspondence with this user, it simply clears the messages contained inside the current conversation.</p>
<form action="/messages/actionDelete" method="post">
<p>
	<input type="hidden" name="msg_convo_id" value="{{$convo_id}}" />
	<input type="submit" value="Delete" />
	<input type="button" value="Cancel" onclick="$.facebox.close()" />
</p>
</form>