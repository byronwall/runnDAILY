{{foreach from=$routes item=route}}
	<li class="route_recent_list">
		<div class="route_item_image">
			<a href="/routes/view.php?id={{$route->id}}">
				<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
			</a>
		</div>
		<div class="route_item_content">
			<p><a href="/routes/view.php?{{$route->id}}">{{$route->name}}</a></p>
			<p>{{$route->distance}}</p>
		</div>
	</li>
{{/foreach}}
{{$more}}
{{if count($routes)}}
	<li class="route_recent_list">
		<div class="route_item_content"><a href="/routes/browse.php?{{$query}}&format=ajax" class="ajax">see more in this table</a></div>
	</li>
{{/if}}