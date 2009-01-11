<h1>Friends</h1>

<ul>
{{foreach from=$users_friends item=friend}}
	<li>
		<div>
			<a href="/community/view_user.php?uid={{$friend->userID}}">{{$friend->username}}</a>
		</div>
	</li>
{{foreachelse}}
	No friends!
{{/foreach}}
</ul>