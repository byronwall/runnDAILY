<div class="grid_12">
<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
<div class="actions">
	<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png"/>New Route</a>
	<a href="/routes/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Search Routes</a>
</div>
</div>
<div class="clear"></div>

<div class="grid_4">
	<div id="route_list">
		<table class="sortable">
			<thead>
				<tr>
					<th class="sort-date">Date</th>
					<th class="sort-alpha">Route Name</th>
					<th class="sort-numeric">Distance</th>
				</tr>
			</thead>
			<tbody>
				{{foreach from=$routes item=route}}
				<tr id="tr_{{$route.r_id}}">
					<td id="td_dist_{{$route.r_id}}">{{$route.r_creation|date_format:"n/j/Y"}}</td>
					<td>
						<p><a href="/routes/view?rid={{$route.r_id}}" class="icon"><img src="/img/icon/route.png" />{{$route.r_name}}</a></p>
						<p><a href="#"	rel={{$route.r_id}} class="route icon"><img src="/img/icon/arrow.png">Show in place</a></p>
<!--						<p><a href="/routes/view?rid={{$route.r_id}}" class="icon"><img src="/img/icon/route.png" /> View in Detail</a></p>-->
					</td>
					<td class="dist-val align_right bold">{{$route.r_distance|round:"2"}} mi</td>
				</tr>
				{{foreachelse}}
				<tr><td colspan="3">You do not currently have any routes.<a href="/routes/create" class="icon"><img src="/img/icon/route_plus.png" />Create</a> a new route to enable advanced features.</td></tr>
				{{/foreach}}
			</tbody>
		</table>
	</div>
	<div id="route_info" style="display:none">
		<h4 id="info_name"></h4>
		<p id="info_distance"></p>
		<p id="info_date"></p>
		<p><a href="#" class="list icon"><img src="/img/icon/arrow_back.png" />Return</a></p>
	</div>
</div>
<div class="grid_8">
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
		$("#info_name").text(routes[rid].r_name);
		$("#info_distance").html('<img src="/img/icon/distance.png" /> Distance: <span class="dist-val">' + routes[rid].r_distance.toFixed(2) + ' mi</span>');
		$("#info_date").html('<img src="/img/icon/calendar.png" /> ' + routes[rid].r_creation);
	},
	switchToAll: function(){
		RouteIndex.route_view = false;
		$("#loading_overlay").show();
		$("#route_list, #route_settings").show();
		$("#route_info").hide();

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
			var id = "#td_dist_" + this.r_id;
			var dist = center.distanceFrom(this.latlng) * meters_to_miles;
			$(id).text(dist.toFixed(2));
		});
	},
	selected_rid: null,
	marker_click_event: function(latlng){
		$(".active_row").removeClass("active_row");
		if(RouteIndex.selected_rid == this.id){
			RouteIndex.selected_rid = null
		}
		else{
			RouteIndex.selected_rid = this.id;
			var id = "#tr_" + this.id;
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
		Map.instance.setCenter(RouteIndex.init_location);
		$("#route_list").height($("#route_map").height()).css("overflow", "auto");
		$("table.sortable").sortTable({
			sort_field: "Date",
			sort_desc: -1
		});
	}
}
var routes = {{$routes_js}};

$(document).ready(RouteIndex.ready_event);
</script>