<div class="grid_12">
	<h2 class="heading float_left">{{$route_view->name}}</h2>
	<h2 class="align_right heading float_right">{{$route_view->distance}} mi</h2>
	<hr class="heading">
</div>
<div class="clear"></div>
<div class="grid_3">
	<p>Created by: <a href="/community/view_user?uid={{$route_view->uid}}">User</a></p>
</div>
<div class="grid_9">
{{if $route_view->getIsOwner($currentUser->uid)}}
	<div class="actions">
	{{if $route_view->getCanEdit()}}
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon_training_plus.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}" class="icon"><img src="/img/icon_pencil_arrow.png" />Edit</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy" class="icon"><img src="/img/icon_maps_pencil.png" />Copy/Edit</a>
		<a href="#delete_modal" class="facebox icon"><img src="/img/icon_delete.png" />Delete</a>
	{{else}}
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon_training_plus.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy" class="icon"><img src="/img/icon_pencil_plus.png" />Copy/Edit</a>
	{{/if}}
	</div>
	<div id="delete_modal" style="display:none">
		<h2>Are you sure you want to delete this route?</h2>
		<form method="POST" action="/routes/action_delete">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="r_rid" value="{{$route_view->id}}" />
			<input type="submit" value="delete" />
			<input type="button" value="cancel" onclick="$.facebox.close()" />
		</form>
	</div>
	<div id="route_train_modal" style="display:none">
		<h2>Record a Time</h2>
		<form action="/training/action_save" method="post" id="route_train_form">
			<input type="hidden" name="t_rid" value="{{$route_view->id}}">
			<input type="hidden" name="action" value="save" />
			<p><label>Time: </label><input type="text" name="t_time" value="00:00:00"></p>
			<p><label>Activity Type: </label>
				<select name="t_type" id="training_type">
				{{foreach from=$t_types item=type}}
					<option value="{{$type.id}}">{{$type.name}}</option>
				{{/foreach}}
				</select>
			</p>
			<p><label>Date: </label><input type="text" name="t_date" value="Today"></p>
			<p><label>Distance: </label><input type="text" name="t_distance" value="{{$route_view->distance}}"></p>
			<p><label>Private? </label><input type="checkbox" name="t_private" value="1"></p>
			<p>
				<input type="submit" value="Create">
				<input type="button" value="Cancel" onclick="$.facebox.close()" />
			</p>
		</form>
	</div>
{{/if}}
</div>
<div class="clear"></div>

<div class="grid_12">
	<div id="map_placeholder" class="map large"></div>
</div>
<div class="clear"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>    
<script type="text/javascript">

$(document).ready( function(){
	Map.load("map_placeholder", null);
	MapData.loadRoute({{$route_view->points}}, false);

	var validator = $("#route_train_form").validate({
		rules: {
			t_time: {
				required: true
			},
			t_date: {
				required: true
			},
			t_distance: {
				required: true,
				number: true
			}
		},
		messages: {
			t_time: {
				required: "Enter a time"
			},
			t_date: {
				required: "Enter a date"
			},
			t_distance: {
				required: "Enter a distance",
				number: "Must be a number"
			}
		},
		// the errorPlacement has to take the table layout into account
		errorPlacement: function(error, element) {
			if ( element.is(":checkbox") )
				error.appendTo ( element.next() );
			else if( element.is(":hidden") )
				alert(error.text());				
			else
				error.appendTo( element.parent() );
		},
		// set this class to error-labels to indicate valid fields
		success: function(label) {
			// set &nbsp; as text for IE
			label.html("&nbsp;").addClass("checked");
		}
	});	
});

document.body.onunload = GUnload;
</script>