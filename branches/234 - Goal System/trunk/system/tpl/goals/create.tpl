<div class="grid_12">
	<h2 id="page-heading">Create a Goal</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<form action="/goals/action_create" method="post" id="goal_create_form">
		<p><label>Name: </label></p>
		<p><input type="text" name="go_name" /></p>
		<p><label>Description: </label></p>
		<p><textarea rows="3" cols="25" name="go_desc"></textarea></p>
		<hr />
		<p>Between <input type="text" name="go_start" /> and <input type="text" name="go_end" />:</p>
		<p>I would like to run <input type="text" name="go_metadata[dist_tot]" /> miles.</p>
		<p>I would like to run at an average pace of <input type="text" name="go_metadata[pace_avg]" /> miles/hour.</p>
		<p>I would like to run for <input type="text" name="go_metadata[time_tot]" /> minutes.</p>
		<p>I would like to run <input type="text" name="go_metadata[freq_tot]" /> times.</p>
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