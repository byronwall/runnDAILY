<div class="grid_12">
	<h2 id="page-heading">New Goal</h2>
</div>
<div class="clear"></div>

<form action="/goals/action_create" method="post" id="goal_create_form">
<div class="grid_12">
	<h2>General Information</h2>
		<p class="notice">Enter a name and description for your goal.</p>
		<p><label>Name: </label></p>
		<p><input type="text" name="go_name" /></p>
		<p><label>Description: </label></p>
		<p><textarea rows="3" name="go_desc"></textarea></p>
	<h2>Date Boundaries</h2>
		<p class="notice">Specify a date range for your goal.</p>
		<p><label>Start: </label></p>
		<p><input type="text" name="go_start" /></p>
		<p><label>End: </label></p>
		<p><input type="text" name="go_end" /></p>
	<h2>Goal Specifics</h2>
		<p class="notice">Specify the conditions of your goal.</p>
		<p>I would like to run <input id="input_dist" type="text" name="go_metadata[dist_tot]" size="6" /> miles.</p>
		<p>I would like to run at an average pace of <input id="input_pace" type="text" name="go_metadata[pace_avg]" size="6" /> miles/hour.</p>
		<p>I would like to run for <input id="input_time" type="text" name="go_metadata[time_tot]" size="6" /> minutes.</p>
		<p><input type="submit" value="Create"></p>
		<div class="error"><p></p></div>
</div>
</form>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(function(){
	$("#goal_create_form").validate({
		rules:{
			go_name:{required:true},
			go_start:{required:true},
			go_end:{required:true}
		},
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {
			  var message = errors == 1
			    ? 'You missed 1 field. It has been highlighted'
			    : 'You missed ' + errors + ' fields. They have been highlighted';
			  $("div.error p").html(message);
			  $("div.error").show();
			} else {
			  $("div.error").hide();
			}
		},
		submitHandler: function(form){
			$("input").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
	$("#input_dist").blur(function(){
		if($("#input_dist").val() != ""){
			var dist = parseFloat($("#input_dist").val());
			if($("#input_pace").val() != ""){
				var pace = parseFloat($("#input_pace").val());
				var goal_time = (1 / pace) * dist * 60;
				if($("#input_time").val() != ""){
					var time = parseFloat($("#input_time").val());
					if(goal_time < time){
						$("#input_time").val(Math.floor(goal_time));
					}
				}else{
					$("#input_time").val(goal_time);
				}
			}else if($("#input_time").val() != ""){
				var time = $("#input_time").val();
				var goal_pace = dist / (time / 60);
				$("#input_pace").val(Math.round(goal_pace * 10) / 10);
			}
		}
	});
});
</script>