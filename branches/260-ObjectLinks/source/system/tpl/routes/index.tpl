<div class="grid_12">
<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
<div class="actions">
	<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png"/>New Route</a>
<!--	<a href="/routes/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Search Routes</a>-->
</div>
</div>
<div class="clear"></div>

<div class="grid_3" id="route_sidebar">
	<div id="sort_options" class="align_right">
		<label>Sort by: </label>
		<select id="sort_select">
			<option value="r_date">Date</option>
			<option value="r_dist">Distance</option>
			<option value="r_name">Route Name</option>
		</select>
		<a href="#" id="reverse_sort" class="sort_desc"><img src="/img/icon/sort_desc.png" /> DESC</a>
	</div>
	<div id="route_list">
	{{foreach from=$routes item=route}}
		<div id="route_{{$route.r_id}}" class="route_item">
			<div><a href="/routes/view/{{$route.r_id}}/{{$route.r_name}}" class="r_name icon"><img src="/img/icon/route.png" />{{$route.r_name}}</a></div>
			<div class="r_date icon"><img src="/img/icon/calendar.png" />{{$route.r_creation|date_format}}</div>
			<div class="icon float_right"><img src="/img/icon/distance.png" /><span class="r_dist dist-val">{{$route.r_distance|round:"2"}} mi</span></div>
			<div class="clear"></div>
			<div><a href="#" rel={{$route.r_id}} class="route icon"><img src="/img/icon/arrow.png" /> Show in place</a></div>
		</div>
	{{foreachelse}}
		<div>You do not have any routes.<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png" />Create</a> a new route to enable advanced features.</div>
	{{/foreach}}
	</div>
	<div id="route_info" style="display:none">
		<h4 id="info_name"></h4>
		<p id="info_distance"></p>
		<p id="info_date"></p>
		<p id="info_desc"></p>
		<p><a href="#" class="list icon"><img src="/img/icon/arrow_back.png" />Return</a></p>
	</div>
</div>

<div class="grid_9">
	<div id="route_map" class="map"></div>
</div>
<div class="clear"></div>

<div id="loading_overlay" style="display:none">
</div>
{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">
var RouteIndex = {
	view_route : false,
	init_location: null,
	temp_rid: null,
	switchToRoute: function(rid){
		RouteIndex.view_route = true;
		RouteIndex.temp_rid = rid;
		//add loading screen
		var div = $("#route_map");
		$("#loading_overlay").show().height(div.height()).width(div.width());
		$("#loading_overlay").css({
			"position":"absolute",
			"top":div.position().top,
			"left":div.position().left,
			"background-color":"#000",
			"opacity":0.5
		});
		//get route data
		if(routes[rid].polyline){
			RouteIndex.route_data_callback(routes[rid].polyline);
		}
		else{
			$.get(
				"/routes/ajax_route_data",
				{rid:rid},
				RouteIndex.route_data_callback,
				"json"
			);
		}
	},
	route_data_callback: function(polyline){
		//remove loading screen
		$("#loading_overlay").hide();
		
		//show route
		MapData.loadRoute(polyline, {
			draggble: false,
			show_points: false
		});
		
		//change to route info panel
		$("#route_list, #route_settings").hide();
		$("#route_info").show();

		var rid = RouteIndex.temp_rid;
		routes[rid].polyline = polyline;
		$("#info_name").html('<a href="/routes/view/'+rid+'" class="r_name icon"><img src="/img/icon/route.png" />'+routes[rid].r_name+'</a>');
		$("#info_distance").html('<img src="/img/icon/distance.png" /> Distance: <span class="dist-val">' + routes[rid].r_distance.toFixed(2) + ' mi</span>');
		$("#info_date").html('<img src="/img/icon/calendar.png" /> ' + routes[rid].r_creation);
		$("#info_date").text(routes[rid].r_description);
		$("#sort_options").hide();
	},
	switchToAll: function(){
		RouteIndex.route_view = false;
		$("#loading_overlay").show();
		$("#route_list, #route_settings").show();
		$("#route_info").hide();
		$("#sort_options").show();

		Map.instance.clearOverlays();
		$.each(routes, function(){
			Map.instance.addOverlay(this.marker);
		});
		Map.instance.setCenter(RouteIndex.init_location);
		$("#loading_overlay").hide();
	},
	moveend_event: function(){
		if(RouteIndex.route_view) return false;
		return false;
		var center = Map.instance.getCenter();
		$.each(routes, function(){
			var id = "#route_" + this.r_id;
			var dist = center.distanceFrom(this.latlng) * meters_to_miles;
			$(id).text(dist.toFixed(2));
		});
	},
	selected_rid: null,
	marker_click_event: function(latlng){
		RouteIndex.switchToRoute(this.id);
		return;
		$(".active_row").removeClass("active_row");
		if(RouteIndex.selected_rid == this.id){
			RouteIndex.selected_rid = null
		}
		else{
			RouteIndex.selected_rid = this.id;
			var id = "#route_" + this.id;
			$(id).addClass("active_row");
		}
	},
	ready_event: function(){
		$("a.route").click(function(){
			RouteIndex.switchToRoute(this.rel);
			return false;
		});
		$("a.list").click(function(){
			RouteIndex.switchToAll();
			return false;
		});
	
		Map.load("route_map", null, {full_height:true});
		GEvent.addListener(Map.instance, "moveend", RouteIndex.moveend_event);
		var init = null;
		$.each(routes, function(){
			this.latlng = new GLatLng(this.r_start_lat, this.r_start_lng);
			this.marker = new GMarker(this.latlng);
			this.marker.id = this.r_id;
			GEvent.addListener(this.marker, "click", RouteIndex.marker_click_event);
			Map.instance.addOverlay(this.marker);
			if(!RouteIndex.init_location){
				RouteIndex.init_location = this.latlng;
			}
		});
		if(RouteIndex.init_location){
			Map.instance.setCenter(RouteIndex.init_location, 12);
		}
		else{
			Map.instance.setCenter(new GLatLng(39.229984356582, -95.2734375), 4);
		}
		$("#route_list").heightBrowser().css("overflow", "auto");

		$.sorter.add("routes", {
			classes: {
				r_name: "alpha",
				r_dist: "numeric",
				r_date: "date"
			},
			parent: "#route_list",
			item: ".route_item",
			sort_desc: -1,
			sort_key: "r_date",
			reverse: "#reverse_sort",
			selector: "#sort_select"
		});
	}
};
var routes = {{$routes_js|default:"{}"}};

$(document).ready(RouteIndex.ready_event);
</script>