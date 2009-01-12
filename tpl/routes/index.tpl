{{* This is the template for the index of the routes folder. *}}
<h2>Route List</h2>

<div id="route_list_container">
{{foreach from=$route_list item=route}}
	<div id="route_list_item">
		<div class="route_list_item_img">
		<a href="/routes/view.php?id={{$route->id}}">
			<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
		</a>
		</div>
		<div id="route_list_info_container">
			<a href="/routes/view.php?id={{$route->id}}">
				<div class="route_list_item_name">{{$route->name}}</div>
			</a>
			<div class="route_list_item_date"><img src="/img/icon_check.png" />{{$route->date_creation}}</div>
		</div>
	</div>
{{foreachelse}}
No data!
{{/foreach}}
</div>

<h2>Recent Activity</h2>
<div id="route_recent_ctain">
{{foreach from=$recent_list item=recent}}
	<div id="recent_item">You {{$recent->desc}} <a href="/routes/view.php?id={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</div>
{{foreachelse}}
No recent activity, do something!
{{/foreach}}
</div>
