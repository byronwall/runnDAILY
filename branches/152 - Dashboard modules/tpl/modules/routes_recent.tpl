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
{{/foreach}}