{{* This is the template for the index of the routes folder. *}}
<div class="centered_container">
	<a href="/routes/create.php" class="mar_lr_10"><img src="/img/route_create.png" /></a>
	<a href="/route/browse.php" class="mar_lr_10"><img src="/img/route_browse.png" /></a>
	<a href="/community/local.php" class="mar_lr_10"><img src="/img/route_local.png" /></a>
</div>

<h2>Route List</h2>

<div id="route_list_container">
{{foreach from=$currentUser->routes item=route}}
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
{{foreachelse}}
No data!
{{/foreach}}
</div>
