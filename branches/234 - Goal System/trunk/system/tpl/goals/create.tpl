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
		<p>I would like to run <input type="text" name="go_metadata[dist_tot]" size="5" /> miles.</p>
		<p>I would like to run at an average pace of <input type="text" name="go_metadata[pace_avg]" size="5" /> miles/hour.</p>
		<p>I would like to run for <input id="input_time" type="text" name="go_metadata[time_tot]" size="5" /> minutes.</p>
		<p><input type="submit" value="Create"></p>
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
		submitHandler: function(form){
			$("input").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		}
	});
});
</script>