<div class="grid_12">
	<h2 id="page-heading">runn Daily</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/routes/create"><img class="icon" src="/img/icon.png" />New Route</a>
		<a href="/training/create"><img class="icon" src="/img/icon.png" />New Training Item</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h3>News & Announcements</h3>
	<ul>
		<li>The site will soon be released to the public!</li>
	</ul>
</div>
<div class="clear"></div>

{{if $currentUser->isAuthenticated}}
	{{modules list=$currentUser->home_modules}}
{{else}}
	<div class="grid_5">
		<div class="box">
			<p>There are several things you can do as a new user!</p>
			
			<ul>
				<li><a href="/login">Login</a></li>
				<li><a href="/register">Register</a></li>
				<li><a href="/routes/create">Create a route</a></li>
			</ul>
		</div>
	</div>
{{/if}}
<div class="clear"></div>