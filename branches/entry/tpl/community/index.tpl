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

<h2>coming soon: your friends</h2>