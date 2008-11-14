{{if $map_current_route}}
<h1>Viewing details of {{$map_current_route->name}}.</h1>

<div id="map_placeholder"></div>
    
<script type="text/javascript">

$(document).ready( function(){
	load("map");
	var polyline = new GPolyline.fromEncoded({{$map_current_route->points}});
	map.addOverlay(polyline);
	var boundingBox = polyline.getBounds();
	map.setCenter(boundingBox.getCenter(), map.getBoundsZoomLevel(boundingBox));
});
document.body.onunload = GUnload();

</script>

{{else}}
No route requested
{{/if}}