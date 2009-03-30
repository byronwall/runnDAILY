{{foreach from=$routes item=route}}
	<a href="/routes/view?rid={{$route->id}}">
	<div class="route_icon">
		<p><img src="/img/route/{{$route->img_src}}" class="map_icon"/></p>
		<p>{{$route->name}}</p>
	</div>
	</a>
{{/foreach}}
{{$more}}
<div class="clear"></div>