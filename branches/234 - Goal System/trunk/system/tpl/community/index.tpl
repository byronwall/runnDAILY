{{* This is the template for the index page of the community folder. *}}
<div class="grid_12">
	<h2 id="page-heading">Community</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Find a User</h4>
		<p class="notice">Search for a user by username or email.</p>
		<div id="error_box"></div>
		<form id="search_form" action="/community/search" method="post">
			<p>
				<input id="input_search" type="text" name="u_search" />
				<input id="search" type="submit" value="Search" />
			</p>
		</form>
		<div id="results"></div>
	<h4>Your Friends</h4>
	{{foreach from=$users_friends item=friend}}
		<div class="float_left">
			<p>
				<a href="/community/view_user?uid={{$friend.u_uid}}" class="icon"><img src="/img/icon/user_friend.png" />{{$friend.u_username}}</a>
			</p>
		</div>
	{{foreachelse}}
		<div class="float_left">
			<p>You have not added any users as friends.</p>
		</div>
	{{/foreach}}
	<div class="clear"></div>
	
	<h4>Recent New Users</h4>
	{{foreach from=$users_recent item=user}}
		<div class="float_left">
			<p>
				<a href="/community/view_user?uid={{$user.u_uid}}" class="icon"><img src="/img/icon/user.png" />{{$user.u_username}}</a>
			</p>
		</div>
	{{foreachelse}}
		<div class="float_left">
			<p>There are no recent new users.</p>
		</div>
	{{/foreach}}
</div>
<div class="clear"></div>

<script type="text/javascript">
$(document).ready(function(){
	$("#search_form").validate({
		onkeyup: false,
		onclick: false,
		onfocusout: false,
		rules: {
			u_search: {
				required: true,
				minlength: 3
			}
		},
		messages: {
			u_search: {
				required: "Please enter a username or email.",
				minlength: "Please enter at least 3 characters."
			}
		},
		errorLabelContainer: "#error_box",
		errorElement: "p",
		submitHandler : function(form){
			$(form).ajaxSubmit({"target" : "#results"});
		}
	});
});
</script>