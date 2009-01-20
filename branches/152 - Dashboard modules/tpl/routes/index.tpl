{{* This is the template for the index of the routes folder. *}}
<div id="route_actions_con">
	<h2>Route Actions</h2>
	<ul>
		<li><a href="/routes/create.php">Create a route</a></li>
	</ul>
</div>

{{foreach from=$page->modules item=module}}
<div id="module_{{$module->id}}">
	<h2>{{$module->title}}</h2>
	{{$module->content}}
</div>
{{/foreach}}

<div id="route_recent_con">
	<h2>Recently Created</h2>
	{{foreach from=$recent_route_list item=route}}
	<ul class="route_recent_list">
		<li class="route_item_image">
			<a href="/routes/view.php?id={{$route->id}}">
				<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
			</a>
		</li>
		<li class="route_item_content">
			<p><a href="/routes/view.php?id={{$route->id}}">{{$route->name}}</a></p>
			<p>{{$route->distance}}</p>
		</li>
	</ul>
{{foreachelse}}
No data!
{{/foreach}}
</div>

<div id="recent_activity_con">
	<h2>Recent Route Activity</h2>
	<ul class="recent_activity_list">
	{{foreach from=$recent_activity_list item=recent}}
		<li class="recent_activity_item">You {{$recent->desc}} <a href="/routes/view.php?id={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</li>
	{{foreachelse}}
		<li class="recent_activity_item">No recent activity, do something!</li>
	{{/foreach}}
	</ul>
</div>

<div id="route_all_con">
	<h2>All Routes</h2>
	<ul class="recent_activity_list">
	{{foreach from=$all_route_list item=route_all}}
		<li class="recent_activity_item"><a href="/routes/view.php?id={{$route_all->id}}">{{$route_all->name}}</a></li>
	{{foreachelse}}
		<li class="recent_activity_item">No routes, <a href="/routes/create.php">create a route</a>.</li>
	{{/foreach}}
	</ul>
</div>
