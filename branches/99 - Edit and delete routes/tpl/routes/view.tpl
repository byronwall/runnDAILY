{{if $map_current_route}}
<h1>Viewing details of {{$map_current_route->name}}.</h1>
<div id="map_placeholder" class="large_map"></div>

{{if $map_current_route->uid eq $currentUser->userID}}
<ul id="creator_actions">
	<h2>creator actions</h2>
	<li><a href="/routes/create.php?rid={{$map_current_route->id}}">edit route</a></li>
	<li><a href="#TB_inline?height=300&width=300&inlineId=delete_modal&modal=true" class="thickbox">
		delete route
	</a></li>
</ul>

<div id="delete_modal" style="display:none">
	<div>Are you sure you want to delete this route?</div>
	<form method="POST" action="/lib/action_routes.php">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="r_rid" value="{{$map_current_route->id}}" />
		<input type="submit" value="delete" />
		<input type="button" value="cancel" onclick="tb_remove()" />
	</form>
</div>
{{/if}}

<div id="form_time_log">

<h2>record a time for this route</h2>
<form action="/lib/action_time_save.php" method="post" onsubmit="sub()">
<input type="hidden" name="route_id" value="{{$map_current_route->id}}">
	<ul>
		<li><label>time</label><input type="text" name="time" value="12:52.6"></li>
		<li><label>date</label><input type="text" name="date" value="today"></li>
		<li><label>distance</label><input type="text" name="distance" value="{{$map_current_route->distance|@round:2}}"></li>
		<li><input type="submit" value="add to log"></li>
	</ul>
</form>

</div>
    
<script type="text/javascript">

$(document).ready( function(){
	load("map_placeholder", null);
	loadRouteFromDB({{$map_current_route->points}}, false);	
});
document.body.onunload = GUnload();

function load_call(){
	
}

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

{{else}}
No route requested
{{/if}}