{{*
This is the template for the page where new routes are created.
*}}

<div id="map_container_left">
	<div id="other_content">
		<h1>create a new route</h1>
		<form action="#" method="get" onsubmit="show_address($('#txt_address').val());return false;">
	    	<input type="text" id="txt_address" value="purdue university">
	    	<input type="submit" value="center map">
		</form>
		<div id="map_distance_holder">
			Distance: 0.0 miles
		</div>
	    
	    <ul id="map_control_panel">
	    	<li><a href="#" onclick="clearAllPoints();return false;">clear all</a></li>
	    	<li><a href="#" onclick="undoLastPoint();return false;">undo</a></li>
	    	<li><a href="#" onclick="outAndBack()">out and back</a></li>
	   	</ul>
	   	
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
	</div>
	<a href="#" onclick="toggleSize();return false;">full screen map</a>
</div>

<div id="map_container_right">
	<div id="map_placeholder" class="fullscreen"></div>
</div>

<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", map_click);
	{{if !$currentUser->location_lat|@is_null}}
	user_options.latlng_start = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
	map.setCenter(user_options.latlng_start);
	{{/if}}

	updateHeight();
});

$(window).resize( 
	updateHeight
);

document.body.onunload = GUnload();

var sidebarVisible = true;

function toggleSize(){
	if(sidebarVisible){
		$("#map_container_left").width(30);
		$("#map_container_right").css("margin-left", "30px");
	}
	else{
		$("#map_container_left").width(300);
		$("#map_container_right").css("margin-left", "300px");
	}
	$("#other_content").toggle();
	sidebarVisible = !sidebarVisible;	
}
function updateHeight(){
	$("#content_container").height($(window).height() - $("#header_ctain").height());
	map.checkResize();	
}

</script>


