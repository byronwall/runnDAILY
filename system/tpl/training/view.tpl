<div class="grid_12">
	<h2 id="page-heading">Detail for Activity on {{$item->date|date_format}}</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
	{{if $item->getIsOwnedBy($currentUser->uid)}}
		<a href="#training_edit_modal" class="facebox icon"><img src="/img/icon_pencil_arrow.png" />Edit</a>
		<a href="#training_delete_modal" class="facebox icon"><img src="/img/icon_delete.png" />Delete</a>
	{{/if}}
	</div>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div class="box">
		<h2>General Info</h2>
		<p>Distance: {{$item->distance}} mi</p>
		<p>Total Time: {{$item->time|time_format}}</p>
		<p>Pace: {{$item->pace}} mph</p>
		<a href="/routes/view/{{$item->route->id}}">View {{$item->route->name}}</a>
	</div>
</div>

<div class="grid_9">
	<div class="box">
		<h2>Week at a Glance</h2>
		{{
			include file=generic/calendar.tpl 
			calendar=$calendar 
			day_week_template="generic/cal_day.tpl" 
			day_mon_template="generic/cal_day.tpl"
		}}
	</div>
</div>

<div class="clear"></div>

{{if $item->getIsOwnedBy($currentUser->uid)}}
	<div id="training_edit_modal" style="display:none">
		<h2>Edit This Entry</h2>
		<form action="/training/action_edit" method="post" id="training_edit_form">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<input type="hidden" name="t_rid" value="{{$item->rid}}" />
			<ul id="train_errors" class="error_box"></ul>
			
			<p>
				<label>Time</label>
				<input type="text" name="t_time" value="{{$item->time|time_format:false}}" />
			</p>
			<p>
				<label>Date</label>
				<input type="text" name="t_date" value="{{$item->date|date_format}}" />
			</p>
			<p>
				<label>Distance</label>
				<input type="text" name="t_distance" value="{{$item->distance}}" />
			</p>
			<p>
				<input type="submit" value="Update" />
				<input type="button" value="Cancel" onclick="$.facebox.close()" />
			</p>
		</form>
	</div>
	
	<div id="training_delete_modal" style="display:none">
		<h2>Are you sure you want to delete this training entry?</h2>
		<form method="POST" action="/training/action_delete">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<input type="submit" value="Delete" />
			<input type="button" value="Cancel" onclick="$.facebox.close()" />
		</form>
	</div>
{{/if}}

<div class="grid_12">
	<h2 id="page-heading">Coming Soon</h2>
</div>
<div class="clear"></div>

<div class="grid_3">
	<div class="box">
		<h2>General Info</h2>
		<p>Calories burned</p>
	</div>
</div>

<div class="grid_5">
	<div class="box">
		<h2>Detailed Info</h2>
		<p>line charts of this log versus others (pace vs min/max)</p>
		<p>calendar for the week of the event</p>
		<p>milestones affected by the log</p>
		<p>links to schedule the same event at a later date</p>
	</div>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Community</h2>
		<p>links to send the log to a friend</p>
		<p>preview of the route run</p>
		<p>graph of the elevation</p>
		<p>charts of how this log affects overall stats</p>
		<p>graph of this time to other runs</p>
	</div>
</div>

<div class="clear"></div>

<script type="text/javascript">

$(document).ready(
	function(){
		$("#training_edit_form").validate({
			rules: {
				t_time: {required: true},
				t_date: {required: true},
				t_distance: {
					required: true,
					number: true
				}
			},
			messages: {
				t_time: {required: "Please enter a time"},
				t_date: {required: "Please enter a date"},
				t_distance: {
					required: "Please enter a distance.",
					number: "Distance must be a number."
				}
			},
			errorLabelContainer: "#train_errors",
			wrapper: "li",
			errorClass: "error"
		});
	}
);
</script>
