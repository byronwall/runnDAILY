<div class="grid_12">
	<h2 id="page-heading">Create a Goal</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<form action="/goals/action_create" method="post" id="goal_create_form">
		<p>Between <input type="text" name="go_start" /> and <input type="text" name="go_end" />:</p>
		<p>I would like to run <input type="text" name="go_dist_tot" /> miles.</p>
		<p>I would like to run at an average pace of <input type="text" name="go_pace_avg" /> miles/hour.</p>
		<p>I would like to run for <input type="text" name="go_time_tot" /> minutes.</p>
		<p><input type="submit" value="Create"></p>
	</form>
</div>
<div class="clear"></div>

<script type="text/javascript">

$(document).ready(function(){
	$("#goal_create_form").validate({
		rules:{
			g_sdate:{required:true},
			g_edate:{required:true}
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