<div class="grid_12">
	{{if $event}}
		<h2 id="page-heading">Edit Event: {{$event->name}}</h2>
	{{else}}
		<h2 id="page-heading">Create an Event</h2>
	{{/if}}
</div>
<div class="clear"></div>

<form id="form_event" action="/events/action_create" method="post">
	<input type="hidden" name="e_eid" value="{{$event->eid}}">
	<input type="hidden" name="e_location_lat" value="{{$event->location_lat}}">
	<input type="hidden" name="e_location_lng" value="{{$event->location_lng}}">
	
<div class="grid_6">
	<div class="box">
		<h2>details</h2>
		<p>
			<label for="e_name">name</label>
			<input type="text" id="e_name" name="e_name" value="{{$event->name}}">
		</p>
		<p>
			<label for="e_desc">description</label>
			<textarea id="e_desc" name="e_desc">{{$event->desc}}</textarea>
		</p>
		<p>
			<label for="e_private">private</label>
			<input type="checkbox" id="e_private" name="e_private" value="1"
			{{if $event->private}}checked{{/if}}>
		</p>
		<p>
			<label for="e_type_id">type</label>
			{{html_options name=e_type_id options=$event_types selected=$event->type_id}}
		</p>
	</div>
</div>
<div class="grid_6">
	<div class="box">
		<h2>when</h2>
		<p>
			<label for="e_start_date">start date</label>
			<input type="text" id="e_start_date" name="e_start_date" value="{{$event->start_date|date_format}}">
		</p>
		<p>
			<label for="e_end_date">end date</label>
			<input type="text" id="e_end_date" name="e_end_date" value="{{$event->end_date|date_format}}">
		</p>
	</div>
</div>
<div class="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>where</h2>
		
		<p>select event location on map</p>
		<div id="event_map" class="map"></div>
	</div>
</div>
<div class="grid_6">
	<div class="box">
		<h2>who</h2>
		
		<p>Do you want to associate this event with a group?</p>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="box">
		<h2>Review and Create</h2>
		
		{{if $event}}
		<input type="submit" value="Update Event">
		{{else}}
		<input type="submit" value="Create Event">
		{{/if}}
	</div>
</div>

</form>
<div class="clear"></div>

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">

$(document).ready(function(){
	Map.load("event_map", register_click);

	{{if $event->location_lat}}
		var LatLngCenter = new GLatLng({{$event->location_lat}}, {{$event->location_lng}});
		Map.instance.setCenter(LatLngCenter, 13);
		register_click(null, LatLngCenter);
	{{else}}
		var LatLngCenter = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
		Map.instance.setCenter(LatLngCenter, 13);
	{{/if}}
	
	
	$("#form_event").validate({
		rules: {
			e_name: {required: true},
			e_start_date: {required: true},
			e_desc: {required: true}
		},
		messages: {},
		submitHandler: function(form){
			if(!formChanged(form)){
				$.facebox("Nothing has changed, so this will not submit.");
				return false;
			}
			$(":input").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
	initForm("#form_event");

	function initForm(form){
		$(form).find(":input").each(function(){
			if($(this).is(":checkbox")){
				$(this).data("init", $(this).checked);
			}
			else{
				$(this).data("init", $(this).val());
			}
		});
	}
	function formChanged(form){
		var dirty = false;
		$(form).find(":input").each(function(){
			if($(this).is(":checkbox")){
				dirty = ($(this).data("init") != $(this).checked);
			}
			else{
				dirty = ($(this).data("init") != $(this).val());
			}
			if(dirty) return false;
		});
		return dirty;
	}

	function register_click(overlay, latlng){
		if(latlng){
			Map.instance.clearOverlays();
			$("[name=e_location_lat]").val(latlng.lat());
			$("[name=e_location_lng]").val(latlng.lng());

			var icon_home = new GIcon();
			icon_home.image = "/img/icon_home.png";
			icon_home.shadow = "";
			icon_home.iconSize = new GSize(16, 16);
			icon_home.shadowSize = new GSize(0, 0);
			icon_home.iconAnchor = new GPoint(8, 8);
			icon_home.infoWindowAnchor = new GPoint(16, 16);
			var icon_home_options = {icon: icon_home, clickable: false};
			var markerOptions = { icon:icon_home, draggable:false };

			Map.instance.addOverlay(new GMarker(latlng, markerOptions));
		}
	}
});

</script>