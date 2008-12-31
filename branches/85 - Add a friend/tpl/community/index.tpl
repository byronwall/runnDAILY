{{*
This is the template for the index page of the community folder.
*}}

<h1>Community Page</h1>

<h2>List of users on the site</h2>

<ul>
{{foreach from=$users_all item=user}}
	<li><a href="/community/view_user.php?uid={{$user->userID}}">{{$user->username}}</a></li>
{{/foreach}}
</ul>

<h2>List of your friends</h2>
<ul>
{{foreach from=$users_friends item=friend}}
	<li><a href="/community/view_user.php?uid={{$friend->userID}}">{{$friend->username}}</a></li>
{{foreachelse}}
	No friends!
{{/foreach}}
</ul>