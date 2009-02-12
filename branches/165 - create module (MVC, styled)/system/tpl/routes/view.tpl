<div class="grid_12">
	<h2 id="page-heading">{{$route_view->name}}</h2>
</div>
<div class="clear"></div>
<div class="grid_2">
	<p>Created by: <a href="/community/view_user?uid={{$route_view->uid}}">User</a></p>
</div>
<div class="grid_9">
{{if $route_view->getIsOwner($currentUser->uid)}}
	<div class="actions">
	{{if $route_view->getCanEdit()}}
		<a href="#TB_inline?&height=300&width=300&inlineId=route_train_modal&modal=true" class="thickbox"><img class="icon" src="/img/icon.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}"><img class="icon" src="/img/icon.png" />Edit</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy"><img class="icon" src="/img/icon.png" />Copy/Edit</a>
		<a href="#TB_inline?&height=100&width=300&inlineId=delete_modal&modal=true" class="thickbox"><img class="icon" src="/img/icon.png" />Delete</a>
	{{else}}
		<a href="#TB_inline?&height=300&width=300&inlineId=route_train_modal&modal=true" class="thickbox"><img class="icon" src="/img/icon.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy"><img class="icon" src="/img/icon.png" />Copy/Edit</a>
	{{/if}}
	</div>
	<div id="delete_modal" style="display:none">
		<h2>Are you sure you want to delete this route?</h2>
		<form method="POST" action="/routes/action_delete">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="r_rid" value="{{$route_view->id}}" />
			<input type="submit" value="delete" />
			<input type="button" value="cancel" onclick="tb_remove()" />
		</form>
	</div>
	<div id="route_train_modal" style="display:none">
		<h2>New Training Item</h2>
		<form action="/training/action_save" method="post" id="route_train_form">
		<input type="hidden" name="t_rid" value="{{$route_view->id}}">
		<input type="hidden" name="action" value="save" />
			<ul>
				<li><label>time</label><input type="text" name="t_time" value="12:52.6"></li>
				<li><label>date</label><input type="text" name="t_date" value="today"></li>
				<li><label>distance</label><input type="text" name="t_distance" value="{{$route_view->distance}}"></li>
				<li><label>private?</label><input type="checkbox" name="t_private" value="1"></li>
				<li><input type="submit" value="add to log"></li>
				<li><input type="button" value="cancel" onclick="tb_remove()" />
			</ul>
		</form>
	</div>
{{/if}}
</div>
<div class="grid_1">
	<p>{{$route_view->distance}} mi<p>
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
	load("map_placeholder", null);
	loadRouteFromDB({{$route_view->points}}, false);

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

function sub(){
	regex = /^(?:(?:(\d+):)?(\d+):)?(\d+(?:\.\d+))$/;
	
	var time_input = $("input[name='time']").val();
	
	seconds_match = regex.exec(time_input);
	
	hours = seconds_match[1]||0;
	minutes = seconds_match[2]||0;
	seconds = seconds_match[3];
	
	seconds = hours * 3600 + minutes * 60 + seconds * 1;
	
	$("input[name='time']").val(seconds);
	return true;
}

</script>