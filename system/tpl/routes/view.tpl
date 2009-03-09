<div class="grid_12">
	<h2 class="heading float_left">{{$route_view->name}}</h2>
	<h2 class="align_right heading float_right">{{$route_view->distance}} mi</h2>
	<hr class="heading">
</div>
<div class="clear"></div>
<div class="grid_3">
	<p>Created by: <a href="/community/view_user.php?uid={{$route_view->uid}}">User</a></p>
</div>
<div class="grid_9">
{{if $route_view->getIsOwner($currentUser->uid)}}
	<div class="actions">
	{{if $route_view->getCanEdit()}}
		<a href="#TB_inline?&height=300&width=300&inlineId=route_train_modal&modal=true" class="thickbox icon"><img src="/img/icon_training_plus.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}" class="icon"><img src="/img/icon_pencil_arrow.png" />Edit</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy" class="icon"><img src="/img/icon_maps_pencil.png" />Copy/Edit</a>
		<a href="#TB_inline?&height=100&width=300&inlineId=delete_modal&modal=true" class="thickbox icon"><img src="/img/icon_delete.png" />Delete</a>
	{{else}}
		<a href="#TB_inline?&height=300&width=300&inlineId=route_train_modal&modal=true" class="thickbox icon"><img src="/img/icon_training_plus.png" />Record Time</a>
		<a href="/routes/create?rid={{$route_view->id}}&mode=copy" class="icon"><img src="/img/icon_pencil_plus.png" />Copy/Edit</a>
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
<div class="clear"></div>

<!--<div class="grid_12">-->
<!--	<h2>route details</h2>-->
<!--	<ul>-->
<!--		<li>distance: {{$route_view->distance}}</li>-->
<!--		<li>creator: <a href="/community/view_user.php?uid={{$route_view->uid}}">{{$route_view->user->username}}</a></li>-->
<!--		<li>training logs: {{$route_view->getTrainingCount()}}</li>-->
<!--		{{if $route_view->getHasParent()}}-->
<!--			<li><a href="/routes/view.php?id={{$route_view->rid_parent}}">view parent route</a></li>-->
<!--		{{/if}}-->
<!--	</ul>-->
<!--</div>-->
<!--<div class="clear"></div>-->

<div class="grid_12">
	<div id="map_placeholder" class="map large"></div>
</div>
<div class="clear"></div>

<!--{{if $route_view->getIsOwner($currentUser->uid)}}-->
<!--	<ul id="creator_actions">-->
<!--		<h2>creator actions</h2>-->
<!--		{{if $route_view->getCanEdit()}}-->
<!--			<li><a href="/routes/create.php?rid={{$route_view->id}}">edit original</a></li>-->
<!--			<li><a href="/routes/create.php?rid={{$route_view->id}}&mode=copy">coming: edit a copy</a></li>-->
<!--			<li><a href="#TB_inline?&height=100&width=300&inlineId=delete_modal&modal=true" class="thickbox">-->
<!--				delete route-->
<!--			</a></li>-->
<!--		{{else}}-->
<!--			<li><a href="/routes/create.php?rid={{$route_view->id}}&mode=copy">coming: edit a copy</a></li>-->
<!--		{{/if}}-->
<!--	</ul>-->
<!--	-->
<!--	<div id="delete_modal" style="display:none">-->
<!--		<h2>Are you sure you want to delete this route?</h2>-->
<!--		<form method="POST" action="/lib/action_routes.php">-->
<!--			<input type="hidden" name="action" value="delete" />-->
<!--			<input type="hidden" name="r_rid" value="{{$route_view->id}}" />-->
<!--			<input type="submit" value="delete" />-->
<!--			<input type="button" value="cancel" onclick="tb_remove()" />-->
<!--		</form>-->
<!--	</div>-->
<!--{{/if}}-->

<!--<div id="form_time_log">-->
<!---->
<!--<h2>route actions</h2>-->
<!--<a href="#TB_inline?&height=300&width=300&inlineId=route_train_modal&modal=true" class="thickbox">record a time for this route</a>-->
<!---->
<!--<div id="route_train_modal" style="display:none">-->
<!--	<h2>enter details of entry</h2>-->
<!--	<form action="/lib/action_training.php" method="post" id="route_train_form">-->
<!--	<input type="hidden" name="t_rid" value="{{$route_view->id}}">-->
<!--	<input type="hidden" name="action" value="save" />-->
<!--		<ul>-->
<!--			<li><label>time</label><input type="text" name="t_time" value="12:52.6"></li>-->
<!--			<li><label>date</label><input type="text" name="t_date" value="today"></li>-->
<!--			<li><label>distance</label><input type="text" name="t_distance" value="{{$route_view->distance}}"></li>-->
<!--			<li><input type="submit" value="add to log"></li>-->
<!--			<li><input type="button" value="cancel" onclick="tb_remove()" />-->
<!--		</ul>-->
<!--	</form>-->
<!--</div>-->
<!---->
<!--</div>-->

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