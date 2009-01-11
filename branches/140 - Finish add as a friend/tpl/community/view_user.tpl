<h1>user details</h1>

<h2>name: {{$user->username}}</h2>
{{if !$currentUser->getIsFriend($user->userID)}}
	<a href="#" id="a_friend" rel="add,{{$user->userID}}">add as friend</a>
{{elseif $currentUser->userID == $user->userID}}
{{else}}
	<a href="#" id="a_friend" rel="remove,{{$user->userID}}">remove as friend</a>
{{/if}}

<h2>coming soon: list of routes by this user</h2>

<h1>send a message</h1>
<form action="/lib/action_message_create.php" method="POST" name="user_message">
	<textarea name="m_msg">enter message</textarea>
	<input type="submit" value="send" />
	<input type="hidden" name="m_uid_to" value="{{$user->userID}}" />
	<input type="hidden" name="m_uid_from" value="{{$currentUser->userID}}" />
</form>

<script type="text/javascript">

$("#a_friend").bind("click", click_friend);

function click_friend(){
	$(this).text("talking to database...");
	var action = this.rel.split(",")[0];
	var uid = this.rel.split(",")[1];

	$("#a_friend").unbind("click", click_friend);
	$("#a_friend").bind("click", function(){ return false; });
	$.post(
		"/lib/ajax_friend.php",
		{f_uid:uid, action:action},
		function(data){
			if(data){
				$("#a_friend").text("complete");
			}
			else{
				$("#a_friend").text("refresh and try again");
			}			
		},
		"json"
	);
	return false;
}
</script>