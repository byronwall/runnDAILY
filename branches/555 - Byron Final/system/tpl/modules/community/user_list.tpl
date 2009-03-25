<ul>
	{{foreach from=$users item=user}}
		<li><a href="/community/view_user?uid={{$user->uid}}">{{$user->username}}</a></li>
	{{/foreach}}
</ul>