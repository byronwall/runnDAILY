{{*
This is the template for the index page of the community folder.
*}}
<div class="grid_12">
	<h2 id="page-heading">Community</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="#"><img class="icon" src="/img/icon_user_silhouette.png" />Find User</a>
		<a href="#"><img class="icon" src="/img/icon_binocular.png" />Find Group</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div class="box">
		<h2>All Users</h2>	
		<ul>
		{{foreach from=$users_all item=user}}
			<li><a href="/community/view_user?uid={{$user->uid}}">{{$user->username}}</a></li>
		{{/foreach}}
		</ul>
	</div>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Friends</h2>
		<ul>
		{{foreach from=$users_friends item=friend}}
			<li><a href="/community/view_user?uid={{$friend->uid}}">{{$friend->username}}</a></li>
		{{foreachelse}}
			No friends!
		{{/foreach}}
		</ul>
	</div>
</div>

<div class="clear"></div>

<div class="grid_12">
	<h2 id="page-heading">Coming Soon</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#"><img class="icon" src="/img/icon_mail_plus.png" />Send PM</a>
	</div>
</div>

<div class="clear"></div>