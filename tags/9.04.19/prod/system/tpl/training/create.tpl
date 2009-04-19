<div class="grid_12">
	<h2 id="page-heading">New Training Item</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
	</div>
</div>
<div class="clear"></div>

<form action="/training/action_save" method="post" id="route_train_form">
<div class="grid_4">
	<div class="box">
		<h2>Step 1</h2>
		<p class="notice">Was your training completed on a familiar route?</p>
		<p><input id="radio_no_route" type="radio" name="t_rid" value="" checked="checked"><label>No</label></p>
		<p><input id="route_radio" type="radio" name="t_rid" value=""><label>Yes</label></p>
		<p>
		<select id="route_select" style="display:none">
			<option value="0">Please select a route</option>
			{{foreach from=$routes item=route}}
				<option value="{{$route->id}}">{{$route->name}} | {{$route->distance}} mi</option>
			{{/foreach}}
		</select>
		</p>
	</div>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Step 2</h2>
		<p class="notice">Please enter the details of your training.</p>
		<p><label>Route: </label><span id="route_name">None selected</span></p>
		<p><label>Distance: </label><input type="text" name="t_distance" value="{{$route_view->distance}}" id="route_distance"></p>
		<p><label>Activity Type: </label>
			<select name="t_type" id="training_type">
					{{foreach from=$t_types item=type}}
						<option value="{{$type.id}}">{{$type.name}}</option>
					{{/foreach}}
			</select></p>
		<p><label>Date: </label><input type="text" name="t_date" value="Today"></p>
		<p><label>Time: </label><input type="text" name="t_time" value="00:00:00"></p>
	</div>
</div>

<div class="grid_4">
	<div class="box">
		<h2>Step 3</h2>
		<p class="notice">Do you wish to make this training item private?</p>
		<p><label>Private? </label><input type="checkbox" name="t_private" value="1"></p>
		<p><input type="submit" value="Create"></p>
	</div>
</div>
</form>

<div class="clear"></div>

<script type="text/javascript">
var routes = {{$routes_json}};

$(document).ready(function(){
	$("#route_radio").click(function(){
			$("#route_select").show();
			if($(this).val()){
				$(this).val($("#route_select :selected").val());
			}
	});
	$("#radio_no_route").click(function(){
		$("#route_select").val(0);
		$("#route_select").hide().change();
		
	});
	$("#route_select").change(function(){
		if($(this).val()>0){
			var route = routes[$(this).val()];	
			$("#route_name").text(route.name);
			$("#route_distance").val(route.distance);
			$("input[name=t_rid]:checked").val($(this).val());
		}
		else{
			$("#route_name").text("None selected");
			$("#route_distance").val("");
			$("input[name=t_rid]:checked").val("");
		}
	});
	$("#route_train_form").validate({
		rules:{
			t_distance:{required:true, number:true},
			t_date:{required:true},
			t_time:{required:true}
		},
		submitHandler: function(form){
			if($("#route_radio").is(":checked") && $("#route_select").val()==0){
				$("#route_radio").attr("disabled", "disabled");
			}
			$("input").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
});
</script>
