{{*
This is the template for the page where new routes are created.
*}}
<div class="grid_12">
{{if $is_edit}}
<h2 id="page-heading">Editing {{$route_edit->name}}</h2>
{{else}}
<h2 id="page-heading">New Route</h2>
{{/if}}
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon/delete.png"/>Clear All Points</a>
		<a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a>
		<a href="#" onclick="MapActions.outAndBack(); return false;" class="icon"><img src="/img/icon/out_back.png"/>Out and Back</a>
		<a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon/fullscreen.png"/>Full Screen</a>
		<a href="#settings_modal" class="facebox icon"><img src="/img/icon/settings.png" />Settings</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_2">
<div class="route_distance">
	<p class="r_distance_disp dist-num">0.00</p>
	<p class="units dist-unit">mi</p>
</div>
<hr>
<div class="" id="route_name_desc">
<div class="delete_box">
<h4>Route Name & Description</h4>
	<form action="/routes/action_create" method="post" id="r_form_save">
		<div id="route_error_box"></div>
		<p><label>Route Name: </label><input type="text" name="r_name" value="{{$route_edit->name}}" class="field"/></p>
		<p><label>Description:</label></p>
		<p><textarea rows="3" name="r_description" class="field">{{$route_edit->description}}</textarea></p>
		<input type="hidden" name="r_distance" value=""/>
		<input type="hidden" name="r_points" value=""/>
		<input type="hidden" name="r_start_lat" value=""/>
		<input type="hidden" name="r_start_lng" value=""/>
		{{if $engine->requirePermission("PV__300")}}
			{{if $is_edit}}
				<input type="hidden" name="r_id" value="{{$route_edit->id}}"/>
				<input type="hidden" name="action" value="update"/>
				<p><input type="submit" value="Update Route"/></p>
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
		<p class="notice">You may re-center the map to an address, city, state, or ZIP.</p>
		<p><input type="text" id="txt_address" value="" class="field"></p>
		<p><input type="submit" value="Re-center"></p>
		<p id="location_msg" class=""></p>
	</form>
</div>
</div>

<div class="grid_10">
	<div id="r_map" class="map map_fix"></div>
</div>
<div id="results" style="display:none"></div>
<div id="map_overlay" style="display:none">
	<img src="/img/logo.png">
	<p><a href="#" onclick="MapActions.clearAllPoints();return false;" class="icon"><img src="/img/icon/delete.png"/>Clear All Points</a></p>
	<p><a href="#" onclick="MapActions.undoLastPoint();return false;" class="icon"><img src="/img/icon_arrow_undo.png"/>Undo Last Point</a></p>
	<p><a href="#" onclick="MapActions.outAndBack()" class="icon"><img src="/img/icon/out_back.png"/>Out and Back</a></p>
	<p><a href="#settings_modal" class="facebox icon"><img src="/img/icon/settings.png" />Settings</a></p>
	<p><a href="#route_name_desc" class="facebox icon"><img src="/img/icon/save.png" />Save</a></p>
	<p><a href="#" onclick="Display.toggle_fullscreen();return false;" class="icon"><img src="/img/icon/fullscreen.png"/>Close Full Screen</a></p>
	
	<div class="route_distance">
		<p class="r_distance_disp dist-num">0.00</p>
		<p class="units dist-unit">miles</p>
	</div>
</div>
<div class="clear"></div>

<div id="settings_modal" style="display: none">
	<h4>Additional Map Options</h4>
	<div id="errors_box" style="display:none"></div>
	<form action="/user/action_map_settings" method="post" id="r_form_settings">
		<p class="notice">Set a few options for the map!</p>
		<p>
			Map type:
			<select id="settings_map_type">
				<option value="G_NORMAL_MAP">Map</option>
				<option value="G_SATELLITE_MAP">Satellite</option>
				<option value="G_HYBRID_MAP">Hybrid</option>
				<option value="G_PHYSICAL_MAP">Terrain</option>
			</select>
		</p>
		<p><label>Mile Marker Distance: </label><input name="mile_dist" type="text" id="u_mile_marker" class="number" value="1.0"/ size="5"><span class="dist-unit"> mi</span></p>
		<p><label>Circular Radius: </label><input type="text" name="circ_dist" id="u_circle_dist" class="number" value="5.0" size="5"/><span class="dist-unit"> mi</span></p>
		<p><label>Display Circular Perimeter? </label><input name="circ_enable" type="checkbox" id="input_circle_show"/></p>
		<p><label>Follow Roads? </label><input type="checkbox" name="dir_enable" id="input_follow_roads"/></p>
		<p>
			<input type="button" value="Apply Once" onclick="check_apply()"/>
			{{if $engine->requirePermission("PV__300")}}
			<input type="submit" value="Apply Default" />
			{{/if}}
		</p>
		<input type="hidden" name="map_settings" >
	</form>
</div>

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">

$(document).ready( function(){
	Map.load("r_map", Map.event_click, {full_height:true});
	GEvent.addListener(Map.instance, "singlerightclick",
		function(point, src, overlay){
			Directions.click(null, Map.instance.fromContainerPixelToLatLng(point), null);
		}
	);

	{{if $currentUser->settings.map_settings}}
		MapSettings = $.extend({}, MapSettings, {{$currentUser->settings.map_settings}});
		Map.instance.setMapType(eval(MapSettings.MapType));
	{{/if}}
	
	{{if !$is_edit and !$currentUser->location_lat|@is_null}}
		Map.setHomeLocation({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
	{{/if}}

	{{if $is_edit}}
		MapData.loadRoute({{$route_edit->points}}, {
			draggable:true
		});
	{{/if}}

	$("#r_form_save").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Please enter a name."}
		},
		errorLabelContainer: "#route_error_box",
		errorElement: "p",
		submitHandler: function(form){
			MapSave.submitHandler(form);
			
			form.submit();
		}
	});

	settings_var = $("#r_form_settings").validate({
		rules: {
			mile:{
				required:true,
				number: true
			},
			circ:{
				required:true,
				number:true
			}		
		},		
		submitHandler : function(form){
			$("[name=map_settings]").val($.toJSON(MapSettings));
			$(form).ajaxSubmit({
				success: function(data){
					if(data){
						$.facebox("Your settings have been saved.", 1000);
					}
					else{
						$.facebox("There was an error, try again.", 1000);
					}
				},
				dataType: "json"
			});
			return false;
		},
		errorLabelContainer: "#errors_box",
		errorElement: "p"
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
	$("#settings_map_type").change(function(){
		MapSettings.MapType = $(this).val();
		Map.instance.setMapType(eval($(this).val()))
	});

	var form_init = {
		"#input_circle_show": MapSettings.DistanceCircle.enable,
		"#input_follow_roads": MapSettings.Directions.enable,
		"#u_circle_dist": MapSettings.DistanceCircle.radius,
		"#u_mile_marker": MapSettings.MileMarkers.distance,
		"#settings_map_type": MapSettings.MapType
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
		else if($(key).is("select")){
			$(key).val(this.toString());
		}
	});

	Units.init({
		callback:function(){
			Map.refresh();
		}
	});
});

function check_apply(){
	if(settings_var.numberOfInvalids() == 0){
		$.facebox.close();
	}
	return;
}

document.body.onunload = GUnload;
</script>