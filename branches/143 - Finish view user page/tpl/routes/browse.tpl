<h1>Browse Routes on Runn Daily</h1>

<form id="route_browse_form" action="/routes/browse.php" method="get">
	<ul>
		<li>username: <input type="text" name="u_username" value="byron"/></li>
		<li>distance: <input type="text" name="r_distance[0]" value="1.0"/><input type="text" name="r_distance[1]" value="5.0" /></li>
		<li>route name: <input type="text" name="r_name" value="spac"/></li>
		<li>date created: <input type="text" name="r_creation[0]" value="yesterday"/><input type="text" name="r_creation[1]" value="today"/></li>
		<li><input type="submit" value="search"/></li>
	</ul>
</form>

<ul>
{{foreach from=$routes item=route}}
	<li class="route_recent_list">
		<div class="route_item_image">
			<a href="/routes/view.php?id={{$route->id}}">
				<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
			</a>
		</div>
		<div class="route_item_content">
			<p><a href="/routes/view.php?id={{$route->id}}">{{$route->name}}</a></p>
			<p>{{$route->distance}}</p>
		</div>
	</li>
{{foreachelse}}
</ul>
No routes found!
{{/foreach}}