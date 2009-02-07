<h1>Create New Training Entry</h1>

<form action="/training/action_save" method="post" id="route_train_form">
	<input type="hidden" name="action" value="save" />
	
	<div>
		<h2>step 1 : route details</h2>		
		<ul>
			<li><input type="radio" name="t_rid" checked="checked" value="">not on a route</li>
			<li>
				<div>
					<input type="radio" name="t_rid" value="">on a route
					<select name="route_select" id="route_select" style="display:none">
						<option value="">--select a route or no route is assumed--</option>
						{{foreach from=$routes item=route}}
							<option value="{{$route->id}}">{{$route->name}} | {{$route->distance}} mi</option>
						{{/foreach}}
					</select>
				</div>					
			</li>
		</ul>
	</div>
	<div>
		<h2>step 2 : entry details</h2>
		<ul>
			<li>route: <span id="route_name">none</span></li>
			<li><label>distance</label><input type="text" name="t_distance" value="{{$route_view->distance}}" id="route_distance"></li>
			<li>activity type:
				<select name="t_type" id="training_type">
						{{foreach from=$t_types item=type}}
							<option value="{{$type.id}}">{{$type.name}}</option>
						{{/foreach}}
					</select>
			</li>
			<li><label>date</label><input type="text" name="t_date" value="today"></li>
			<li><label>time</label><input type="text" name="t_time" value="00:00:00"></li>
		</ul>	
	</div>
	<div>
		<h2>step 3 : privacy details</h2>
		<ul>
			<li>make private<input type="checkbox" name="t_private" value="1"></li>
		</ul>	
	</div>
	<div>
		<h2>step 4 : submit</h2>
		<ul>
			<li><input type="submit" value="add to log"></li>
		</ul>	
	</div>
</form>

<script type="text/javascript">
var routes = {{$routes_json}};

$(document).ready(function(){
	$("input[name=t_rid]").change(function(){
			$("#route_select").toggle();
			if($(this).val()){
				$(this).val($("#route_select :selected").val());
			}
	});
	$("#route_select").change(function(){
		if($(this).val()>0){
			var route = routes[$(this).val()];	
			$("#route_name").text(route.name);
			$("#route_distance").val(route.distance);
			$("input[name=t_rid]:checked").val($(this).val());
		}
		else{
			$("#route_name").text("none");
			$("#route_distance").val("");
			$("input[name=t_rid]:checked").val("");
		}
	});
	$("#route_train_form").validate({
		rules:{
			t_distance:{required:true, number:true},
			t_date:{required:true},
			t_time:{required:true}
		}
	});
});
</script>






