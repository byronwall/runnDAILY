{{if $map_current_route}}
<h1>Viewing details of {{$map_current_route->name}}.</h1>
<div id="map_placeholder"></div>

{{if $map_current_route->uid eq $currentUser->userID}}
<h2>You created this map!!
coming soon: ability to edit, delete, reconfigure your routes!</h2>
{{/if}}

<div id="form_time_log">
<h2>record a time for this route</h2>
<form action="/lib/action_time_save.php" method="post" onsubmit="sub()">
<input type="hidden" name="route_id" value="{{$map_current_route->id}}">
<ul>
<li><label>time</label><input type="text" name="time" value="12:52.6"></li>
<li><label>date</label><input type="text" name="date" value="today"></li>
<li><label>distance</label><input type="text" name="distance" value="{{$map_current_route->distance|@round:2}}"></li>

<li><input type="submit" value="add to log"></li>
</ul>

</form>

</div>

<div id="seconds"></div>
    
<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", null);
	var polyline_options = {{$map_current_route->points}};
	polyline_options.zoomFactor = 2;
	polyline_options.numLevels=18;
	var polyline = new GPolyline.fromEncoded(polyline_options);

	var boundingBox = polyline.getBounds();
	map.setCenter(boundingBox.getCenter(), map.getBoundsZoomLevel(boundingBox));
	
	for(var k = 0; k < polyline.getVertexCount(); k++){
		var latlng = polyline.getVertex(k);
		var markerOptions = { icon:tinyIcon, draggable:false };
		var markerNew = new GMarker(latlng, markerOptions);
		
		var point = new routePoint();
		point.marker = markerNew;
		point.latlng = latlng;
		
		route_points.push(point);
	}
	map_options.draggable = false;
	map_refreshAll();
});
document.body.onunload = GUnload();


function sub(){
	regex = /^(?:(?:(\d+):)?(\d+):)?(\d+(?:\.\d+))$/;
	
	var time_input = $("input[name='time']").val();
	
	seconds_match = regex.exec(time_input);
	
	hours = seconds_match[1]||0;
	minutes = seconds_match[2]||0;
	seconds = seconds_match[3];
	
	seconds = hours * 3600 + minutes * 60 + seconds;
	
	$("input[name='time']").val(seconds);
	
	$("#seconds").text(seconds);

}

</script>

{{else}}
No route requested
{{/if}}