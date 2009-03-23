<div class="grid_12">
	<h2 id="page-heading">Viewing Event: {{$event_view->name}}</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
<div class="actions">
	{{if $event_view->uid == $currentUser->uid}}
		<a href="/events/create?eid={{$event_view->eid}}" class="icon"><img src="/img/icon.png" />Edit Event</a>
		<a href="#event_delete_modal" class="facebox icon"><img src="/img/icon.png" />Delete Event</a>
	{{else}}
		<a href="#event_join_modal" class="facebox icon"><img src="/img/icon.png" />Attend Event</a>
		<a href="#event_leave_modal" class="facebox icon"><img src="/img/icon.png" />Leave Event</a>
	{{/if}}
</div>
</div>
<div class="clear"></div>

<div class="grid_6">
	<div class="box">
		<h2>details</h2>
		<p>	name: {{$event_view->name}}	</p>
		<p>	type: {{$event_view->type}}	</p>
		<p>	description: {{$event_view->desc}}	</p>
		<p>	private: {{$event_view->private}}	</p>
	</div>
</div>
<div class="grid_6">
	<div class="box">
		<h2>when</h2>
		<p>	start date: {{$event_view->start_date|date_format}}	</p>
		<p>	end date: {{$event_view->end_date|date_format}}	</p>
	</div>
</div>

<div class="clear"></div>
<div class="grid_6">
	<div class="box">
		<h2>where</h2>
		
		{{if $event_view->location_lat}}
		<div id="event_map" class="map"></div>
		{{/if}}
	</div>
</div>
<div class="grid_6">
	<div class="box">
		<h2>who</h2>
		
		<p>will list people are attending or the assoc. group</p>
	</div>
</div>
<div class="clear"></div>

<!-- HIDDEN ELEMENTS -->
<div id="event_delete_modal" style="display:none">
	<div>
		<form method="post" action="/events/delete">
			<input type="hidden" name="e_eid" value="{{$event_view->eid}}">
			<p>Are you sure you want to delete this event?</p>
			<p>
				<input type="submit" value="Delete">
				<input type="button" value="Cancel" onclick="$.facebox.close()">
			</p>
		</form>
	</div>
</div>
<div id="event_join_modal" style="display:none">
	<div>
		<form method="post" action="/events/join" id="form_join">
			<input type="hidden" name="e_eid" value="{{$event_view->eid}}">
			<p>Are you sure you want to attend this event?</p>
			<p>
				<input type="submit" value="Attend">
				<input type="button" value="Cancel" onclick="$.facebox.close()">
			</p>
		</form>
	</div>
</div>
<div id="event_leave_modal" style="display:none">
	<div>
		<form method="post" action="/events/leave" id="form_leave">
			<input type="hidden" name="e_eid" value="{{$event_view->eid}}">
			<p>Are you sure you want to leave this event?</p>
			<p>
				<input type="submit" value="Leave">
				<input type="button" value="Cancel" onclick="$.facebox.close()">
			</p>
		</form>
	</div>
</div>

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">
$(document).ready(function(){
	{{if $event_view->location_lat}}
		Map.load("event_map", null);
		var LatLngCenter = new GLatLng({{$event_view->location_lat}}, {{$event_view->location_lng}});
		Map.instance.setCenter(LatLngCenter, 13);

		var icon_home = new GIcon();
		icon_home.image = "/img/icon_home.png";
		icon_home.shadow = "";
		icon_home.iconSize = new GSize(16, 16);
		icon_home.shadowSize = new GSize(0, 0);
		icon_home.iconAnchor = new GPoint(8, 8);
		icon_home.infoWindowAnchor = new GPoint(16, 16);
		var icon_home_options = {icon: icon_home, clickable: false};
		var markerOptions = { icon:icon_home, draggable:false };

		Map.instance.addOverlay(new GMarker(LatLngCenter, markerOptions));
	{{/if}}
	
	$("#form_join").validate({
		submitHandler: function(form){
			$.facebox.close();
			$(form).ajaxSubmit({
				dataType:"json",
				success:function(data){
					alert(data);				
				}
			});
		}
	});
	$("#form_leave").validate({
		submitHandler: function(form){
			$.facebox.close();
			$(form).ajaxSubmit({
				dataType:"json",
				success:function(data){
					alert(data);				
				}
			});
		}
	});
});
</script>
