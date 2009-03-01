{{*
This is the template for the page where new routes are created.
*}}
<div class="grid_8">
{{if $is_edit}}
<h2 class="page_head">Editing {{$route_edit->name}}</h2>
{{else}}
<h2 class="page_head">Create a New Route</h2>
{{/if}}
</div>
<div class="grid_4">
	<form action="#" method="get" onsubmit="show_address($('#txt_address').val());return false;">
		<input type="text" id="txt_address" value="Purdue University">
		<input type="submit" value="Re-center">
	</form>
</div>
<div class="clear"></div>

<div class="grid_12">
<hr />
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" onclick="clearAllPoints();return false;">clear all</a>
		<a href="#" onclick="undoLastPoint();return false;">undo</a>
		<a href="#" onclick="outAndBack()">out and back</a>
		<a href="#" onclick="toggleSize();return false;">full screen map</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_4">
<div class="box">
<h2>Route Name & Description</h2>
	<form action="/routes/action_create" method="post" onsubmit="saveSubmit(this)" id="r_form_save">
		<p><label>Route Name: </label><input type="text" name="r_name" value="{{$route_edit->name}}"/></p>
		<p><label>Description: </label><input type="text" name="r_description" value="{{$route_edit->description}}"/></p>
		<input type="hidden" name="r_distance" value=""/>
		<input type="hidden" name="r_points" value=""/>
		<input type="hidden" name="r_start_lat" value=""/>
		<input type="hidden" name="r_start_lng" value=""/>
		{{if $currentUser->isAuthenticated}}
			{{if $is_edit}}
				<input type="hidden" name="r_id" value="{{$route_edit->id}}"/>
				{{if $isCopy}}
					<input type="hidden" name="r_rid_parent" value="{{$route_edit->id}}"/>
					<input type="hidden" name="action" value="save"/>
					<input type="submit" value="Create Route"/>
				{{else}}
					<input type="submit" value="Edit Route"/>
					<input type="hidden" name="action" value="update"/>
				{{/if}}
			{{else}}
				<input type="hidden" name="action" value="save"/>
				<input type="submit" value="Create Route"/>
			{{/if}}
		{{/if}}
	</form>
</div>
</div>
<div class="grid_4">
	<div class="box">
		<h2>Route Options</h2>
		<form id="r_form_settings">
			<p><label>Mile Marker Distance: </label><input type="text" id="u_mile_marker" class="number" value="1.0"/></p>
			<p><label>Circular Radius: </label><input type="text" id="u_circle_dist" class="number" value="5.0"/></p>
			<p><label>Display Radial Perimeter? </label><input type="checkbox" id="input_circle_show"/></p>
		</form>
	</div>
</div>
<div class="grid_4">
	<div class="box">
		<h2>Route Information</h2>
		<p id="r_distance_disp">0.00 mi</p>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div id="r_map" class="map large"></div>
</div>
<div class="clear"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script src="/js/PolylineEncoder.pack.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready( function(){
	load("r_map", map_click);
	
	{{if !$is_edit and !$currentUser->location_lat|@is_null}}
		user_options.latlng_start = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
		map.setCenter(user_options.latlng_start, 13);
	{{/if}}

	//updateHeight();
	{{if $is_edit}}
		loadRouteFromDB({{$route_edit->points}}, true);
	{{/if}}

	var validator = $("#r_form_save").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Enter a name"}
		},
		submitHandler: function(form){
			saveSubmit($(form));
			
			form.submit();
		}
	});
	$("#u_mile_marker").blur(function(){
		mileDistance = $("#u_mile_marker").val();
		map_refreshAll();
	});
	$("#input_circle_show").click(function(){
		circle_show = $(this).attr("checked");
		map_refreshAll();
	});
	$("#u_circle_dist").blur(function(){
		circle_distance = $("#u_circle_dist").val();
		map_refreshAll();
	});
});

$(window).resize( 
	//updateHeight
);

document.body.onunload = GUnload;

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