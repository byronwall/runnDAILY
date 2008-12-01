{{*
This is the template for the page where new routes are created.
*}}
<h1>create a new route</h1>
<form action="#" method="get" onsubmit="show_address($('#txt_address').val());return false;">
    	<input type="text" id="txt_address" value="purdue university">
    	<input type="submit" value="center map">
</form>
<div id="map_placeholder" class="large_map"></div>

<div id="other_content">
	<div id="map_distance_holder">Distance: 0.0 miles</div>
    <div id="map_control_panel">
    	<a href="#" onclick="clearAllPoints();return false;">clear all</a>
    	<a href="#" onclick="undoLastPoint();return false;">undo</a>
    	<a href="#" onclick="outAndBack()">out and back</a>
   	</div>
   	
    {{if $currentUser->isAuthenticated}}
    <div id="map_controls">
    	<h2>save route</h2>
    	<form action="/lib/action_routes.php?action=save" method="post" onsubmit="saveSubmit(this)">
    	<ul>	
    		<li><label for="input_routename">route name</label><input id="input_routename" type="text" name="routeName"></li>
    		<li><label for="input_desc">description</label><input id="input_desc" type="text" name="r_description"></li>
    		<input type="submit" value="save">
    		<input type="hidden" name="distance">
    		<input type="hidden" name="points">
    		<input type="hidden" name="comments">
    		<input type="hidden" name="start_lat">
    		<input type="hidden" name="start_lng">
    	</ul>
   		</form>
	</div>
    {{/if}}   	
    <a href="#" onclick="alert('not implemented');return false;">full screen map</a>
</div>

<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", map_click);
	{{if !$currentUser->location_lat|@is_null}}
	user_options.latlng_start = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
	map.setCenter(user_options.latlng_start);
	{{/if}}
});

document.body.onunload = GUnload();

</script>


