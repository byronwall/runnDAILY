{{*This is the template for the index page of the root folder.*}}<div class="grid_12">	<h2 id="page-heading">runn Daily</h2></div><div class="clear"></div><div class="grid_12">	<div class="actions">		<a href="/routes/create" class="icon"><img src="/img/icon_route_plus.png" />New Route</a>		<a href="/training/create" class="icon"><img src="/img/icon_training_plus.png" />New Training Item</a>	</div></div><div class="clear"></div><div class="grid_12">	<h3>News & Announcements</h3>	<ul>
		<li>The site will soon be released to the public!</li>
	</ul>
</div>
<div class="clear"></div>

<div class="grid_3">
</div>

{{if $currentUser->isAuthenticated}}
	{{modules list=$currentUser->home_modules}}{{else}}
	<div class="grid_5">		<div class="box">			<p>There are several things you can do as a new user!</p>						<ul>				<li><a href="/login">Login</a></li>				<li><a href="/register">Register</a></li>				<li><a href="/routes/create">Create a route</a></li>			</ul>		</div>	</div>{{/if}}
<div class="clear"></div>