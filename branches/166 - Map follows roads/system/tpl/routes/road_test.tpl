<div class="grid_12">
	<h2 id="page-heading">routes / road_test</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
<div id="results" style="display:none"></div>
<div id="map" class="map large"></div>

</div>
<div class="clear"></div>

{{include file="routes/parts/script.tpl"}}

<script type="text/javascript">
$(document).ready(function(){
	Directions.init();
	load("map", Directions.click);
});
Directions = {
	instance: null,
	polyline: null,
	route: null,
	options: {
		getPolyline: true,
		travelMode: G_TRAVEL_MODE_WALKING,
		avoidHighways: true
	},
	init: function(){
		Directions.instance = new GDirections(null, $("#results")[0]);
		GEvent.addListener(Directions.instance, "load", Directions.load_event); 
		GEvent.addListener(Directions.instance, "error", Directions.error_event);
	},
	click: function(overlay, latlng, overlaylatlng){
		if(latlng){
			console.log("points clicked");
			if(route_points.length == 0){
				addPoint(latlng);
			}
			else{
				var prev_point = route_points[route_points.length - 1].latlng;
				var str = "from:" + prev_point.lat() + "," + prev_point.lng() + " to: " + latlng.lat() + "," + latlng.lng()
				Directions.instance.load(str, Directions.options);
			}
		}
	},
	load_event: function(){
		console.log("directions loaded");
		Directions.polyline = Directions.instance.getPolyline();
		var points = Directions.polyline.getVertexCount();
		for(var i = 0; i < points; i++){
			addPoint(Directions.polyline.getVertex(i));
		}
	},
	error_event: function(){
		console.log("directions error");
		console.log(Directions.instance.getStatus());
	}
}
</script>