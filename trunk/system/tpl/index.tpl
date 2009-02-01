{{*
This is the template for the index page of the root folder.
*}}
<div id="route_actions_con" class="actions_con">
	<h2>Home Actions</h2>
	<ul>
		<li><a href="/routes/create">Create a route</a></li>
	</ul>
</div>

{{if $currentUser->isAuthenticated}}
<div id="welcome_con">
	<h2>Personal Dashboard</h2>
	<p>{{$currentUser->username}}, welcome to your personalized dashboard! More features are comming soon!</p>
</div>
<div id="recent_activity_con">
	<h2>Recent Activity</h2>
	<ul class="recent_activity_list">
	{{foreach from=$recent_activity_list item=recent}}
		<li class="recent_activity_item">You {{$recent->desc}} <a href="/routes/view?rid={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</li>
	{{foreachelse}}
		<li class="recent_activity_item">No recent activity, do something!</li>
	{{/foreach}}
	</ul>
</div>
{{else}}
There are several things you can do as a new user!

<a href="/login">Login</a>
<a href="/register">Register</a>
<a href="/routes/create">Create a route</a>

{{/if}}

<div id="news_con">
	<h2>News & Announcements</h2>
	<ul class="news_list">
		<li class="news_item">The site will soon be released to the public!</li>
	</ul>
</div>