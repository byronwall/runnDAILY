{{* This is the template for the index of the routes folder. *}}
<h1>Routes Page</h1>

<a href="/routes/create.php"><img src="/img/route_create.png" /></a>

<h2>list of routes on the site</h2>

<div id="route_list_container">
{{foreach from=$route_list item=route}}
	<div id="route_list_item">
		<div class="route_list_item_img">
		<a href="/routes/view.php?id={{$route->id}}">
			<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->getRoundedDistance()}}" />
		</a>
		</div>
		<div id="route_list_info_container">
			<a href="/routes/view.php?id={{$route->id}}">
				<div class="route_list_item_name">{{$route->name}}</div>
			</a>
			<div class="route_list_item_date"><img src="/img/icon_check.png" />{{$route->date_creation}}</div>
		</div>
	</div>
{{/foreach}}
</div>