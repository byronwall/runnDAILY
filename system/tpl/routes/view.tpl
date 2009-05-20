<div class="grid_12">
	<h2 class="heading float_left">{{$route_view->name}}</h2>
	<h2 class="align_right heading float_right"><span class="dist-val">{{$route_view->distance}} mi</span></h2>
	<hr class="heading">
</div>
<div class="clear"></div>
<div class="grid_3">
	<p>Created by: <a href="/community/view_user/{{$route_view->uid}}/{{$route_view->data.u_username}}">{{$route_view->data.u_username}}</a></p>
</div>
<div class="grid_9">
	<div class="actions">
{{if $route_view->getCanEdit()}}
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon/training_plus.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}" class="icon"><img src="/img/icon_pencil_arrow.png" />Edit</a>
		<a href="#copy_modal" class="facebox icon"><img src="/img/icon_maps_pencil.png" />Copy</a>
		<a href="#delete_modal" class="facebox icon"><img src="/img/icon_delete.png" />Delete</a>
	
		<div id="delete_modal" style="display:none">
			<h2>Are you sure you want to delete this route?</h2>
			<form method="POST" action="/routes/action_delete">
				<input type="hidden" name="action" value="delete" />
				<input type="hidden" name="r_rid" value="{{$route_view->id}}" />
				<input type="submit" value="delete" />
				<input type="button" value="cancel" onclick="$.facebox.close()" />
			</form>
		</div>
	{{else}}
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon/training_plus.png" />Record Time</a>
		<a href="#copy_modal" class="facebox"><img src="/img/icon_pencil_plus.png" />Copy</a>
	{{/if}}
	</div>
	
	<div id="copy_modal" style="display:none">
		<h2>Copy This Route</h2>
		<p>This action will copy the route in our database.  This will allow you to edit the route or simply have your own
		copy.  This is required if you want to edit a route that has training entries associated with it.  This will allow
		all of the training entries to maintain their integrity.</p>
		<p>This is also nice if you want to start with a route you have already completed and just change a couple of points.</p>
		<p>Finally, if you simply want to change this route, and the link is available (between copy and record time), then you 
		may click cancel.</p>
		
		<form id="form_copy" action="/routes/create" method="post">
			<ul id="errors_box"></ul>
			<input type="hidden" name="r_id" value="{{$route_view->id}}">
			<p><label>New Name</label><input type="text" name="r_name" value="{{$route_view->name}} (Copy)"></p>
			<p><a href="/routes/action_copy_edit" class="submit">Copy and Immediately Edit</a></p>
			<p><a href="/routes/action_copy_view" class="submit">Copy and View</a></p>
			<input type="button" onclick="$.facebox.close()" value="Cancel">
		</form>
	</div>
	
	<div id="route_train_modal" style="display:none">
		<h2>Record a Time</h2>
		<form action="/training/action_save" method="post" id="route_train_form">
			<ul id="train_errors" class="error_box"></ul>
		
			<input type="hidden" name="t_rid" value="{{$route_view->id}}">
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
</div>
<div class="clear"></div>

<div class="grid_12">
	<div id="map_placeholder" class="map large"></div>
</div>
<div class="clear"></div>

{{if $route_view->description}}
<div class="grid_12">
	<p><span class="bold">Description:</span> {{$route_view->description}}</p>
</div>
<div class="clear"></div>
{{/if}}

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">

$(document).ready( function(){
	Map.load("map_placeholder", null);
	MapData.loadRoute({{$route_view->points}}, {
		draggable: false,
		show_points: false
	});

	$("#route_train_form").validate({
		rules: {
			t_time: {required: true},
			t_date: {required: true},
			t_distance: {
				required: true,
				number: true
			}
		},
		messages: {
			t_time: {required: "Please enter a time."},
			t_date: {required: "Please enter a date."},
			t_distance: {
				required: "Please enter a distance.",
				number: "Distance must be a number."
			}
		},
		errorLabelContainer: "#train_errors",
		wrapper: "li",
		errorClass: "error"
	});

	$("#form_copy").validate({
		rules: {
			r_name: {required: true}
		},
		errorLabelContainer: "#errors_box",
		wrapper: "li",
		errorClass: "error"
	});	
	$("a.submit").click(function(){
		var form = $("#form_copy");
		form.attr("action", this.href);
		if(form.valid()){
			form.submit();
		}
		return false;
	});
});

document.body.onunload = GUnload;
</script>