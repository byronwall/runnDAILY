{{* This is the template for the index of the routes folder. *}}
<div class="grid_12">
	<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/routes/create" class="icon"><img src="/img/icon_route_plus.png" />New Route</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div class="box">
		<h2>All Routes</h2>
		<ul class="recent_activity_list">
		{{foreach from=$all_route_list item=route_all}}
			<li class="recent_activity_item"><a href="/routes/view?rid={{$route_all->id}}">{{$route_all->name}}</a></li>
		{{foreachelse}}
			<li class="recent_activity_item">No routes, <a href="/routes/create.php">create a route</a>.</li>
		{{/foreach}}
		</ul>
	</div>
</div>

<div class="grid_5">
	<div class="box">
		<h2>Recently Created</h2>
		<ul id="route_recent_con">
		{{include file="routes/parts/route_list.tpl" routes=$recent_route_list}}
		</ul>
	</div>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Recent Route Activity</h2>
		<ul class="recent_activity_list">
		{{foreach from=$recent_activity_list item=recent}}
			<li class="recent_activity_item">You {{$recent->desc}} <a href="/routes/view?rid={{$recent->route->id}}">{{$recent->route->name}}</a>. {{$recent->familiar}}.</li>
		{{foreachelse}}
			<li class="recent_activity_item">No recent activity, do something!</li>
		{{/foreach}}
		</ul>
	</div>
</div>
<div class="clear"></div>

<!--<div id="route_all_con">-->
<!--	<h2>All Routes</h2>-->
<!--	<ul class="recent_activity_list">-->
<!--	{{foreach from=$all_route_list item=route_all}}-->
<!--		<li class="recent_activity_item"><a href="/routes/view?rid={{$route_all->id}}">{{$route_all->name}}</a></li>-->
<!--	{{foreachelse}}-->
<!--		<li class="recent_activity_item">No routes, <a href="/routes/create.php">create a route</a>.</li>-->
<!--	{{/foreach}}-->
<!--	</ul>-->
<!--</div>-->

<div style="display:none" id="preview_map">
	<div id="map_placeholder" class="map large"></div>
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