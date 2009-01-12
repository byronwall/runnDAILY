{{* This is the template for the index of the routes folder. *}}
<div class="centered_container">
	<a href="/routes/create.php" class="mar_lr_10"><img src="/img/route_create.png" /></a>
	<a href="/route/browse.php" class="mar_lr_10"><img src="/img/route_browse.png" /></a>
	<a href="/community/local.php" class="mar_lr_10"><img src="/img/route_local.png" /></a>
</div>

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
			<a href="#TB_inline?&height=500&width=500&inlineId=preview_map" rel='{{$route->points}}' title="{{$route->name}}" class="preview">preview</a>
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