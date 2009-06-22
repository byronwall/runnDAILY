<div class="grid_12">
	<h2 class="heading float_left">{{$route_view->name}}</h2>
	<h2 class="align_right heading float_right"><span class="dist-val">{{$route_view->distance}} mi</span></h2>
	<hr class="heading">
</div>
<div class="clear"></div>
<div class="grid_3">
	<p>Created by: <a href="/community/view_user/{{$route_view->uid}}/{{$route_view->user->username}}">{{$route_view->user->username}}</a></p>
</div>
<div class="grid_9">
	<div class="actions">
		{{if $training_items}}<a href="#assoc_training_items" class="icon"><img src="/img/icon/training.png" />View Training Items</a>{{/if}}
		<a href="#route_train_modal" class="facebox icon"><img src="/img/icon/training_plus.png" />Record Time</a>
		<a href="#copy_modal" class="facebox icon"><img src="/img/icon/route_copy.png" />Copy</a>
	{{if $route_view->getCanEdit()}}
		<a href="/routes/create?rid={{$route_view->id}}" class="icon"><img src="/img/icon/edit.png" />Edit</a>
		<a href="#delete_modal" class="facebox icon"><img src="/img/icon/delete.png" />Delete</a>
	
		<div id="delete_modal" style="display:none">
			<h4>Are you sure you wan to delete the current route?</h4>
				<p class="alert_red">Once a route has been deleted, there is no way to
				recover it! Only delete a route that you are sure you no longer want!</p>
				<form method="POST" action="/routes/action_delete">
				<p>
					<input type="hidden" name="action" value="delete" />
					<input type="hidden" name="r_rid" value="{{$route_view->id}}" />
					<input type="submit" value="Delete" />
					<input type="button" value="Cancel" onclick="$.facebox.close()" />
				</p>
			</form>
		</div>
	{{/if}}
	</div>
	
	<div id="copy_modal" style="display:none">
		<h4>Create a Copy of "{{$route_view->name}}"</h4>
		<p>This action will place a copy of the current route and add it to your route list.
		Doing so will allow you to edit the copied route. Alternatively you may copy a
		route into your account without modifying it. Routes that have training items
		associated with them must be copied before they can be edited. This is required
		in order to maintain the integrity of the training items associated with the
		route.</p>
		<p>Copying the current route is also useful if you would like to use the
		current route as a template for a new route. Simply copy the current route and
		then change the appropriate points or add additional points as necessary.</p>
		
		<form id="form_copy" action="/routes/create" method="post">
			<div id="copy_error_box"></div>
			<input type="hidden" name="r_id" value="{{$route_view->id}}">
			<p><label>New Route Name: </label><input type="text" name="r_name" value="{{$route_view->name}} (Copy)" size="50"></p>
			<p><a href="/routes/action_copy_edit" class="submit icon"><img src="/img/icon/route_copy.png" /> Copy then Edit</a> the New Route</p>
			<p><a href="/routes/action_copy_view" class="submit icon"><img src="/img/icon/route_copy_view.png" /> Copy then View</a> the New Route</p>
			<p><input type="button" onclick="$.facebox.close()" value="Cancel"></p>
		</form>
	</div>
	
	<div id="route_train_modal" style="display:none">
		<h4>Record a Time</h4>
		<p class="notify">Create a training item for the current route.</p>
		<form action="/training/action_save" method="post" id="route_train_form">
			<div id="train_error_box"></div>
			<input type="hidden" name="t_rid" value="{{$route_view->id}}">
			<p><label>Time: </label><input type="text" name="t_time" value="00:00:00" size="10"></p>
			<p><label>Activity Type: </label>
				<select name="t_type" id="training_type">
				{{foreach from=$t_types item=type}}
					<option value="{{$type.id}}">{{$type.name}}</option>
				{{/foreach}}
				</select>
			</p>
			<p><label>Date: </label><input type="text" name="t_date" value="Today" size="15"></p>
			<p><label>Distance: </label><input type="text" name="t_distance" value="{{$route_view->distance}}" size="6"> mi</p>
			<p>Comment:</p>
			<p><textarea rows="5" cols="25" name="t_comment"></textarea></p>
			<p>
				<input type="submit" value="Create">
				<input type="button" value="Cancel" onclick="$.facebox.close()" />
			</p>
		</form>
	</div>
</div>
<div class="clear"></div>

{{if $route_view->description}}
<div class="grid_12">
	<p><span class="bold">Description:</span> {{$route_view->description}}</p>
</div>
<div class="clear"></div>
{{/if}}

<div class="grid_12">
	<div id="map_placeholder" class="map large"></div>
</div>
<div class="clear"></div>

{{if $route_view->elevation}}
<div class="grid_12">
	<h5>Elevation Profile</h5>
	<div id="elev_chart" style="width:100%;height:200px"></div>
</div>
<div class="clear"></div>
{{/if}}

{{if $training_items}}
<div class="grid_12">
	<h5 id="assoc_training_items">Associated Training Items</h5>
	{{counter start=-1 print=false}}
	{{foreach from=$training_items item=training_item}}
	<div id="item_{{counter}}" class="training_item">
			<div class="icon float_left"><img src="/img/icon/distance.png" /><span class="t_dist dist-val">{{$training_item.t_distance|round:"2"}} mi</span></div>
			<div class="t_date icon float_right">{{$training_item.t_date|date_format}} <img src="/img/icon/calendar.png" /></div>
		<div class="clear"></div>
			<div class="icon float_left"><img src="/img/icon/dashboard.png" /><span class="t_pace">{{$training_item.t_pace|round:"2"}} mi/h</span></div>
			<div class="icon float_right">{{$training_item.t_time|time_format}}<span class="t_time" style="display:none">{{$training_item.t_time}}</span> <img src="/img/icon/clock.png" /></div>
		<div class="clear"></div>
		{{if $training_item.t_comment}}
		<div class="align_left italic">{{$training_item.t_comment}}</div>
		{{/if}}
	</div>
	{{/foreach}}
</div>
<div class="clear"></div>
{{/if}}

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">

$(document).ready( function(){
	{{if $route_view->elevation}}
	var elevation = {{$route_view->elevation}};
	var plot_options = {
		yaxis:{
			tickFormatter: function(number){ return number + "m"; }
		},
		legend:{
			show:false
		}
	}
	$.plot($("#elev_chart"), [{label:"meters", data:elevation}], plot_options);
	{{/if}} 
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
		errorLabelContainer: "#train_error_box",
		errorElement: "p"
	});

	$("#form_copy").validate({
		rules: {
			r_name: {required: true}
		},
		messages: {
			r_name: {required: "Please enter a name." }
		},
		errorLabelContainer: "#copy_error_box",
		errorElement: "p"
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