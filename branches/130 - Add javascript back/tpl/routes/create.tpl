{{*
This is the template for the page where new routes are created.
*}}

<div id="map_container_left">
	<div id="other_content">
		{{if $is_edit}}
			<h1>editing {{$route_edit->name}}</h1>
		{{else}}	
			<h1>create a new route</h1>
		{{/if}}
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
		    	<form action="/lib/action_routes.php" method="post" onsubmit="saveSubmit(this)">
		    	<ul>	
		    		<li>
		    			<label for="input_routename">route name</label>
		    			<input id="input_routename" type="text" name="r_name" value="{{$route_edit->name}}">
		    		</li>
		    		<li>
		    			<label for="input_desc">description</label>
		    			<input id="input_desc" type="text" name="r_description" value="{{$route_edit->comments}}">
		    		</li>		    
		    		<input type="hidden" name="r_distance">
		    		<input type="hidden" name="r_points">
		    		<input type="hidden" name="r_start_lat">
		    		<input type="hidden" name="r_start_lng">
		    		{{if $is_edit}}
		    			<input type="hidden" name="r_id" value="{{$route_edit->id}}" />
		    			{{if $isCopy}}
		    				<input type="hidden" name="r_rid_parent" value="{{$route_edit->id}}" />
		    				<input type="hidden" name="action" value="save" />
			    			<input type="submit" value="create">
		    			{{else}}
			    			<input type="submit" value="update">
		    				<input type="hidden" name="action" value="edit" />
		    			{{/if}}		    			
		    		{{else}}
		    			<input type="hidden" name="action" value="save" />
		    			<input type="submit" value="create">
		    		{{/if}}
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

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script src="/js/PolylineEncoder.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", map_click);
	
	{{if !$is_edit and !$currentUser->location_lat|@is_null}}
		user_options.latlng_start = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
		map.setCenter(user_options.latlng_start, 13);
	{{/if}}

	updateHeight();
	{{if $is_edit}}
		loadRouteFromDB({{$route_edit->points}}, true);
	{{/if}}	
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


