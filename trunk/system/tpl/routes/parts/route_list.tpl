{{foreach from=$routes item=route}}
	<a href="/routes/view?rid={{$route->id}}">
	<div class="route_icon">
		<p><img src="/routes/image?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" /></p>
		<p>{{$route->name}}</p>
	</div>
	</a>
{{/foreach}}
<div class="clear"></div>
{{$more}}
{{if count($routes)}}
	<li class="route_recent_list">
		<div class="route_item_content"><a href="/routes/browse?{{$query}}&format=ajax" class="ajax">see more in this table</a></div>
	</li>
{{/if}}