{{*
This is the template for the page where new routes are created.
*}}
<div class="grid_12">
{{if $is_edit}}
<h2 id="page-heading">Editing {{$route_edit->name}}</h2>
{{else}}
<h2 id="page-heading">Create a New Route</h2>
{{/if}}
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon_delete.png"/>Clear All Points</a>
		<a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a>
		<a href="#" onclick="MapActions.outAndBack()" class="icon"><img src="/img/icon_out_back.png"/>Out and Back</a>
		<a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon_magnifier_zoom_fit.png"/>Full Screen</a>
	</div>
</div>
<div class="clear"></div>
<div class="grid_2">
<div class="route_distance">
	<p class="r_distance_disp dist-num">0.00</p>	<p class="units dist-unit">miles</p></div>
<hr>
<div class="" id="route_name_desc">
<div class="delete_box">
<h4>Route Name & Description</h4>
	<form action="/routes/action_create" method="post" id="r_form_save">
		<p class="notice">Go ahead and name your route!.. describe it too</p>
		<p><label>Route Name: </label><input type="text" name="r_name" value="{{$route_edit->name}}" class="field"/></p>
		<p><label>Description</label></p>
		<p><textarea rows="3" name="r_description" class="field">{{$route_edit->description}}</textarea></p>
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
					<p><input type="submit" value="Create Route"/></p>
				{{else}}
					<input type="hidden" name="action" value="update"/>
					<p><input type="submit" value="Edit Route"/></p>
				{{/if}}
			{{else}}
				<input type="hidden" name="action" value="save"/>
				<p><input type="submit" value="Create Route"/></p>
			{{/if}}
		{{/if}}
	</form>
</div>
</div>
<div class="" id="route_re_center">
	<h4>Re-center the Map</h4>
	<form action="#" method="get" onsubmit="Geocoder.showAddress('#txt_address');return false;" class="search">
		<p class="notice">Center the map using ZIP, city, state, or an address.</p>
		<p><input type="text" id="txt_address" value="Purdue University" class="field"></p>
		<p><input type="submit" value="Re-center"></p>
		<p id="location_msg" class=""></p>
	</form>
</div>
<div class="" id="route_options">
	<div class="delete_box">
		<a href="#settings" class="facebox icon">Settings</a>
	</div>
</div>

</div>

<div class="grid_10">
	<div id="r_map" class="map large"></div>
</div>
<div id="results" style="display:none"></div>
<div id="map_overlay" style="display:none">
	<img src="/img/logo.png">
	<ul>
		<li><a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon_delete.png"/>Clear All Points</a></li>
		<li><a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a></li>
		<li><a href="#" onclick="MapActions.outAndBack()" class="icon"><img src="/img/icon_out_back.png"/>Out and Back</a></li>
		<li><a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon_magnifier_zoom_fit.png"/>Close Full Screen</a></li>
		<li><a href="#settings" class="facebox icon">Settings</a></li>
		<li><a href="#route_name_desc" class="facebox icon">Save</a></li>
	</ul>
	
	<div class="route_distance">
		<p class="r_distance_disp dist-mi">0.00 mi</p>
	</div>
</div>
<div class="clear"></div>

<div id="settings" style="display: none">
	<h4>Additional Map Options</h4>

	<form action="/user/action_map_settings" method="post" id="r_form_settings">
		<p class="notice">Set a few options for the map!</p>
		<p><label>Mile Marker Distance: </label><input type="text" id="u_mile_marker" class="number" value="1.0"/><span class="dist-unit">mi</span></p>
		<p><label>Circular Radius: </label><input type="text" id="u_circle_dist" class="number" value="5.0"/><span class="dist-unit">mi</span></p>
		<p><label>Display Radial Perimeter? </label><input type="checkbox" id="input_circle_show"/></p>
		<p><label>Follow Roads? </label><input type="checkbox" id="input_follow_roads"/></p>
		<p><input type="submit" value="Set Default" /></p>
		<input type="hidden" name="u_settings[map_settings]" >
	</form>
</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script src="/js/PolylineEncoder.pack.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready( function(){
	Map.load("r_map", Map.event_click, {full_height:true});
	GEvent.addListener(Map.instance, "singlerightclick",
		function(point, src, overlay){
			Directions.click(null, Map.instance.fromContainerPixelToLatLng(point), null);
		}
	);

	{{if $currentUser->isAuthenticated && $currentUser->settings.map_settings}}
		MapSettings = $.extend({}, MapSettings, {{$currentUser->settings.map_settings}});
	{{/if}}
	
	{{if !$is_edit and !$currentUser->location_lat|@is_null}}
		Map.setHomeLocation({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
	{{/if}}

	{{if $is_edit}}
		loadRouteFromDB({{$route_edit->points}}, true);
	{{/if}}

	$("#r_form_save").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Enter a name"}
		},
		submitHandler: function(form){
			MapSave.submitHandler(form);
			
			form.submit();
		}
	});

	$("#r_form_settings").validate({
		submitHandler : function(form){
			$("[name=u_settings\[map_settings\]]").val($.toJSON(MapSettings));
			$(form).ajaxSubmit();
			$.facebox.close();
		}
	});
	
	$("#u_mile_marker").change(function(){
		MapSettings.MileMarkers.distance = $("#u_mile_marker").val();
		Map.refresh();
	});
	$("#input_circle_show").click(function(){
		MapSettings.DistanceCircle.enable = $(this).attr("checked");
		Map.refresh();
	});
	$("#input_follow_roads").click(function(){
		MapSettings.Directions.enable = $(this).attr("checked");
	});
	$("#u_circle_dist").change(function(){
		MapSettings.DistanceCircle.radius = $("#u_circle_dist").val();
		Map.refresh();
	});

	var form_init = {
		"#input_circle_show": MapSettings.DistanceCircle.enable,
		"#input_follow_roads": MapSettings.Directions.enable,
		"#u_circle_dist": MapSettings.DistanceCircle.radius,
		"#u_mile_marker": MapSettings.MileMarkers.distance
	}

	$.each(form_init, function(key){
		if($(key).is(":checkbox")){
			if(this == true){
				$(key).attr("checked", true);
			}
			else{
				$(key).removeAttr("checked");
			}
		}
		else if($(key).is(":text")){
			$(key).val(this);
		}
	});

	Units.init({
		callback:function(){
			Map.refresh();
		}
	});
});

document.body.onunload = GUnload;
</script>