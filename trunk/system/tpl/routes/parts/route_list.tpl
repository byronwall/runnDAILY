{{foreach from=$routes item=route}}
	<p>
	<a href="/routes/view?rid={{$route->id}}">
		<img src="/routes/image?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
		{{$route->name}} ({{$route->distance}})
	</a>
	</p>
{{/foreach}}
{{$more}}
{{if count($routes)}}
	<li class="route_recent_list">
		<div class="route_item_content"><a href="/routes/browse?{{$query}}&format=ajax" class="ajax">see more in this table</a></div>
	</li>
{{/if}}