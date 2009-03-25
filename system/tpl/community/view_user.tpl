<div class="grid_12">
	<h2 id="page-heading">{{$user->username}}</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		{{if !$currentUser->checkFriendsWith($user->uid)}}
		<a href="#addFriend" id="a_addfriend" class="icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>
		{{else}}
		<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>
		{{/if}}
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
<h5 style="color: red;">Advanced user features are currently disabled while we work through our server issues.</h5>
</div>

<div class="clear"></div>

<div id="message_modal" style="display:none">
	<h1>Send {{$user->username}} a message.</h1>
	<form action="/message/create" method="POST" name="user_message">
		<textarea name="m_msg">Message text.</textarea>
		<input type="submit" value="Send" />
		<input type="button" value="Cancel" onclick="$.facebox.close()" />
		<input type="hidden" name="m_uid_to" value="{{$user->uid}}" />
		<input type="hidden" name="m_uid_from" value="{{$currentUser->uid}}" />
	</form>
</div>

<script type="text/javascript">
var f_uid = {{$user->uid}};

$("#a_addfriend").live("click", function(){
	var a = $(this);
	a.text("adding friend...");
	
	$.post(
		"/community/add_friend",
		{f_uid:f_uid},
		function(data){
			if(data){
				a.replaceWith('<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_minus.png" />Remove Friend</a>');
			}
			else{
				a.text("Try To Add Again");
				a.hide();
				a.fadeIn("slow");
			}
		},
		"text"
	);
	return false;	
});

$("#a_removefriend").live("click", function(){
	var a = $(this);
	a.text("removing friend...");
	
	$.post(
		"/community/ajax_remove_friend",
		{f_uid:f_uid},
		function(data){
			if(data){
				a.replaceWith('<a href="#addFriend" id="a_removefriend" class="icon"><img src="/img/icon/user_plus.png" />Add as Friend</a>');
			}
			else{
				a.text("Try To Remove Again");
				a.hide();
				a.fadeIn("slow");
			}
		},
		"text"
	);
	return false;	
});

</script>