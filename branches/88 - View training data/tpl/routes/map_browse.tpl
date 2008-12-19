<div id="map_placeholder"></div>

<ul id="content_route_list"></ul>

<script type="text/javascript">

$(document).ready(
	function(){
		load("map_placeholder");
		event_moveListener = GEvent.addListener(map, "moveend", map_moveend);
		map_moveend();
	}
);
document.body.onunload = GUnload();

map_routes_on_map = [];

function map_moveend(){
	var center = map.getBounds();
	
	var sw_corner = center.getSouthWest();
	var ne_corner = center.getNorthEast();
	
	$.getJSON(
		"/lib/ajax_browse_routes.php",
		{ 	sw_corner_lat : sw_corner.lat(),
			sw_corner_lng : sw_corner.lng(),
			ne_corner_lat : ne_corner.lat(),
			ne_corner_lng : ne_corner.lng(),
			action : "list"
		},				 
		function(data){
			$.each(data, function(index, route){
				if(!map_routes_on_map[route.id]){							
					var marker = new GMarker(new GLatLng(route.start_lat, route.start_lng));
					marker.bindInfoWindowHtml("<h1>"+route.name+"</h1><h2>distance: "+route.distance+"</h2><a href='#' onclick='map_load_route("+route.id+");return false;'>view</a>");							
					map.addOverlay(marker);
					map_routes_on_map[route.id] = true;
					
					$("#content_route_list").append("<li><a href='#' onclick='map_load_route("+route.id+");return false;'>view "+route.name+"</a></li>");
				}					
			})
		}
	);

}
function map_load_route(id){
	$.getJSON(	
		"/lib/ajax_browse_routes.php",
		{	action : "view",
			route_id : id
		},
		function(route){									
			GEvent.removeListener(event_moveListener);
			map.clearOverlays();
			var polyline = new GPolyline.fromEncoded($.parseJSON(route.points));
			map.addOverlay(polyline);
			map.setCenter(polyline.getVertex(0));
		}
	);
}

</script>