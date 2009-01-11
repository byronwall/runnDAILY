<h1>user details</h1>

<h2>name: {{$user->username}}</h2>
<a href="#">coming soon: add as friend</a>
<h2>coming soon: list of routes by this user</h2>

<h1>send a message</h1>
<form action="/lib/action_message_create.php" method="POST" name="user_message">
	<textarea name="m_msg">enter message</textarea>
	<input type="submit" value="send" />
	<input type="hidden" name="m_uid_to" value="{{$user->userID}}" />
	<input type="hidden" name="m_uid_from" value="{{$currentUser->userID}}" />
</form>