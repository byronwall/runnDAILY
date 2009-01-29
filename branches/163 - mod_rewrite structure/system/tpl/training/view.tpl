<h1>viewing details</h1>

<h2>general info</h2>
<div>distance: {{$item->distance}} mi</div>
<div>time: {{$item->time|time_format}}</div>
<div>pace: {{$item->pace}} mph</div>
<div>date: {{$item->date|date_format}}</div>
<a href="/routes/view/{{$item->route->id}}">view {{$item->route->name}}</a>

<h2>better info</h2>
<h3>other logs for the user that week</h3>
{{
	include file=generic/calendar.tpl 
	calendar=$calendar 
	day_week_template="training/cal_day.tpl" 
	day_mon_template="training/cal_day.tpl"
}}
{{if $item->getIsOwnedBy($currentUser->uid)}}
	<ul id="training_manage">
		<h2>manage the log</h2>
		<li><a href="#TB_inline?&height=150&width=300&inlineId=training_edit_modal&modal=true" class="thickbox">
			edit
		</a></li>
		<li><a href="#TB_inline?&height=100&width=400&inlineId=training_delete_modal&modal=true" class="thickbox">
			delete
		</a></li>
	</ul>
	
	<div id="training_edit_modal" style="display:none">
		<h2>edit this entry</h2>
		<form action="/lib/action_training.php" method="post" id="training_edit_form">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<input type="hidden" name="action" value="edit" />			
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
		<form method="POST" action="/lib/action_training.php">
			<input type="hidden" name="t_tid" value="{{$item->tid}}" />
			<input type="hidden" name="action" value="delete" />
			<input type="submit" value="delete" />
			<input type="button" value="cancel" onclick="tb_remove()" />
		</form>
	</div>
{{/if}}
<h3>coming soon</h3>
<h4>general info</h4>
pace<br>
distance<br>
date run<br>
calories burned<br>
<h4>more detailed info</h4>
line charts of this log versus others (pace vs min/max)<br>
calendar for the week of the event<br>
milestones affected by the log<br>
links to schedule the same event at a later date<br>
<h4>community</h4>
links to send the log to a friend<br>
preview of the route run<br>
graph of the elevation<br>
charts of how this log affects overall stats<br>
graph of this time to other runs<br>

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
