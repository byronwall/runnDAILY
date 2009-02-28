<div class="grid_12">
	<h2 id="page-heading">Detail for Activity on {{$item->date|date_format}}</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
	{{if $item->getIsOwnedBy($currentUser->uid)}}
		<a href="#TB_inline?&height=150&width=300&inlineId=training_edit_modal&modal=true" class="thickbox icon"><img src="/img/icon.png" />Edit</a>
		<a href="#TB_inline?&height=100&width=400&inlineId=training_delete_modal&modal=true" class="thickbox icon"><img src="/img/icon.png" />Delete</a>
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
		<a href="/routes/view?rid={{$item->route->id}}">View {{$item->route->name}}</a>
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
<!--	<ul id="training_manage">-->
<!--		<h2>manage the log</h2>-->
<!--		<li><a href="#TB_inline?&height=150&width=300&inlineId=training_edit_modal&modal=true" class="thickbox">-->
<!--			edit-->
<!--		</a></li>-->
<!--		<li><a href="#TB_inline?&height=100&width=400&inlineId=training_delete_modal&modal=true" class="thickbox">-->
<!--			delete-->
<!--		</a></li>-->
<!--	</ul>-->
	
	<div id="training_edit_modal" style="display:none">
		<h2>edit this entry</h2>
		<form action="/training/action_edit" method="post" id="training_edit_form">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<ul>
				<li><label>time</label><input type="text" name="t_time" value="{{$item->time|time_format}}" /></li>
				<li><label>date</label><input type="text" name="t_date" value="{{$item->date|date_format}}" /></li>
				<li><label>distance</label><input type="text" name="t_distance" value="{{$item->distance}}" /></li>
				<li><input type="submit" value="edit" /></li>
				<li><input type="button" value="cancel" onclick="tb_remove()" /></li>
			</ul>
		</form>
	</div>
	
	<div id="training_delete_modal" style="display:none">
		<h2>Are you sure you want to delete this route?</h2>
		<form method="POST" action="/training/action_delete">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<input type="submit" value="delete" />
			<input type="button" value="cancel" onclick="tb_remove()" />
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
		var validator = $("#training_edit_form").validate({
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
	}
);
</script>
