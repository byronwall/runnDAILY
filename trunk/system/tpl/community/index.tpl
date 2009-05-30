{{* This is the template for the index page of the community folder. *}}
<div class="grid_12">
<h2 id="page-heading">Community</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
<div class="actions">
<!--	<a href="#" class="icon"><img src="/img/icon/binocular.png" />Find User</a>-->
</div>
</div>
<div class="clear"></div>

<div class="grid_12">
<h4>Find a User</h4>
<p class="notice">Search for a user by username or email.</p>
<p>
	<input id="input_search" type="text" name="u_search" />
	<input id="search" type="submit" value="Search" />
</p>
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

<h4>Recently Joined Users</h4>
{{foreach from=$users_all item=user}}
	<div class="float_left">
		<p>
			<a href="/community/view_user?uid={{$user.u_uid}}" class="icon"><img src="/img/icon/user.png" />{{$user.u_username}}</a>
		</p>
	</div>
{{foreachelse}}
	<div class="float_left">
		<p>There are currently no recent users.</p>
	</div>
{{/foreach}}
</div>
<div class="clear"></div>

<script type="text/javascript">
$(document).ready(function(){
	$("#search").click(function(){
		$("#results").val("<p>Searching...</p>");
		$("#results").load("search", {search_for : $("#input_search").val()});
	});
});
</script>