{{*
This is the template for the index page of the root folder.
*}}
<div class="grid_12">
<div class="article">
	<h4>News & Announcements</h4>
	<ul>
		<li>The site will soon be released to the public!</li>
	</ul>
</div>
</div>
<div class="clear"></div>
<div class="grid_4">
<div class="box">
	<h2>Home Actions</h2>
	<ul>
		<li><a href="/routes/create.php">Create a route</a></li>
	</ul>
</div>
</div>

{{if $currentUser->isAuthenticated}}
<div class="grid_4">
<div class="box">
	<h2>Personal Dashboard</h2>
	<p>{{$currentUser->username}}, welcome to your personalized dashboard! More features are comming soon!</p>
</div>
</div>
<div class="grid_4">
<div class="box">
	<h2>Recent Activity</h2>
	<ul>
	{{foreach from=$recent_activity_list item=recent}}
		<li>You {{$recent->desc}} <a href="/routes/view.php?id={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</li>
	{{foreachelse}}
		<li>No recent activity, do something!</li>
	{{/foreach}}
	</ul>
</div>
</div>
{{else}}
<div class="grid_4">
<div class="box">
<p>There are several things you can do as a new user!</p>

<ul>
<li><a href="/login.php">Login</a></li>
<li><a href="/register.php">Register</a></li>
<li><a href="/routes/create.php">Create a route</a></li>
</ul>
</div>
</div>
{{/if}}
<div class="clear"></div>