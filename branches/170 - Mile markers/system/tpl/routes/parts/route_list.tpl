{{foreach from=$routes item=route}}
	<a href="/routes/view?rid={{$route->id}}">
	<div class="route_icon">
		<p><img src="/routes/image?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" class="map_icon"/></p>
		<p>{{$route->name}}</p>
	</div>
	</a>
{{/foreach}}
{{$more}}
{{if count($routes)}}
	<a href="/routes/browse?{{$query}}&format=ajax" class="ajax">
	<div class="route_icon">
		<p><img src="/img/icon.png"/></p>
		<p>See more entries..</p>
	</div>
	</a>
{{/if}}
<div class="clear"></div>