{{* This is the template for the index of the routes folder. *}}
<div id="route_actions_con">
	<h2>Route Actions</h2>
	<ul>
		<li><a href="/routes/create.php">Create a route</a></li>
	</ul>
</div>

<div id="route_recent_con">
	<h2>Recently Created</h2>
	{{foreach from=$recent_route_list item=route}}
	<ul class="route_recent_list">
		<li class="route_item_image">
			<a href="/routes/view.php?id={{$route->id}}">
				<img src="/lib/image_route.php?encoded={{$route->getEncodedString()}}&distance={{$route->distance|@round:2}}" />
			</a>
		</li>
		<li class="route_item_content">
			<p><a href="/routes/view.php?id={{$route->id}}">{{$route->name}}</a></p>
			<p>{{$route->distance}}</p>
			<p><a href="#TB_inline?&height=500&width=500&inlineId=preview_map" rel='{{$route->points}}' title="{{$route->name}}" class="preview">preview</a></p>
		</li>
	</ul>
{{foreachelse}}
No data!
{{/foreach}}
</div>

<div id="recent_activity_con">
	<h2>Recent Route Activity</h2>
	<ul class="recent_activity_list">
	{{foreach from=$recent_activity_list item=recent}}
		<li class="recent_activity_item">You {{$recent->desc}} <a href="/routes/view.php?id={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</li>
	{{foreachelse}}
		<li class="recent_activity_item">No recent activity, do something!</li>
	{{/foreach}}
	</ul>
</div>

<div id="route_all_con">
	<h2>All Routes</h2>
	<ul class="recent_activity_list">
	{{foreach from=$all_route_list item=route_all}}
		<li class="recent_activity_item"><a href="/routes/view.php?id={{$route_all->id}}">{{$route_all->name}}</a></li>
	{{foreachelse}}
		<li class="recent_activity_item">No routes, <a href="/routes/create.php">create a route</a>.</li>
	{{/foreach}}
	</ul>
</div>

<div style="display:none" id="preview_map">
	<div id="map_placeholder" class="large_map"></div>
</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>    
<script type="text/javascript">
var init = false;
$(document).ready(function(){
	$("a.preview").click( function(){
		if(!init){
			load("map_placeholder", null);
			init = !init;
		}			
		tb_show(this.title, this.href, false);
		map.checkResize();
		loadRouteFromDB($.parseJSON(this.rel));
		
		return false;
	});
}
);

</script>