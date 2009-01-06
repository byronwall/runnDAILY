<h1>Viewing details of {{$route_view->name}}.</h1>

<ul>
	<h2>route details</h2>
	<li>distance: {{$route_view->distance}}</li>
	<li>creator: <a href="/community/view_user.php?uid={{$route_view->uid}}">{{$route_view->user->username}}</a></li>
	<li>training logs: {{$route_view->getTrainingCount()}}</li>
	{{if $route_view->getHasParent()}}
		<li><a href="/routes/view.php?id={{$route_view->rid_parent}}">view parent route</a></li>
	{{/if}}
	
</ul>

<div id="map_placeholder" class="large_map"></div>

{{if $route_view->getIsOwner($currentUser->userID)}}
	<ul id="creator_actions">
		<h2>creator actions</h2>
		{{if $route_view->getCanEdit()}}
			<li><a href="/routes/create.php?rid={{$route_view->id}}">edit original</a></li>
			<li><a href="/routes/create.php?rid={{$route_view->id}}&mode=copy">coming: edit a copy</a></li>
			<li><a href="#TB_inline?height=300&width=300&inlineId=delete_modal&modal=true" class="thickbox">
				delete route
			</a></li>
		{{else}}
			<li><a href="/routes/create.php?rid={{$route_view->id}}&mode=copy">coming: edit a copy</a></li>
		{{/if}}
	</ul>
	
	<div id="delete_modal" style="display:none">
		<div>Are you sure you want to delete this route?</div>
		<form method="POST" action="/lib/action_routes.php">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="r_rid" value="{{$route_view->id}}" />
			<input type="submit" value="delete" />
			<input type="button" value="cancel" onclick="tb_remove()" />
		</form>
	</div>
{{/if}}

<div id="form_time_log">

<h2>record a time for this route</h2>
<form action="/lib/action_time_save.php" method="post" onsubmit="sub()">
<input type="hidden" name="route_id" value="{{$route_view->id}}">
	<ul>
		<li><label>time</label><input type="text" name="time" value="12:52.6"></li>
		<li><label>date</label><input type="text" name="date" value="today"></li>
		<li><label>distance</label><input type="text" name="distance" value="{{$route_view->distance}}"></li>
		<li><input type="submit" value="add to log"></li>
	</ul>
</form>

</div>
    
<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", null);
	loadRouteFromDB({{$route_view->points}}, false);	
});
document.body.onunload = GUnload();

function sub(){
	regex = /^(?:(?:(\d+):)?(\d+):)?(\d+(?:\.\d+))$/;
	
	var time_input = $("input[name='time']").val();
	
	seconds_match = regex.exec(time_input);
	
	hours = seconds_match[1]||0;
	minutes = seconds_match[2]||0;
	seconds = seconds_match[3];
	
	seconds = hours * 3600 + minutes * 60 + seconds;
	
	$("input[name='time']").val(seconds);
}

</script>