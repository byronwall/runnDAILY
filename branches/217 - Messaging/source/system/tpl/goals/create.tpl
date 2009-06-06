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
		
		<div id="date_section">
			<p>
				<input id="date_year" type="radio" name="js_date"/>
				<label for="date_year">this year</label>
<!--			</p>-->
<!--			<p>-->
				<input id="date_month" type="radio" name="js_date"/>
				<label for="date_month">this month</label>
<!--			</p>-->
<!--			<p>-->
				<input id="date_week" type="radio" name="js_date"/>
				<label for="date_week">the next week</label>
			</p>
			<p>
				<input id="date_days" type="radio" name="js_date"/>
				<label for="date_days">the next </label>
				<input type="text" id="date_days_text" disabled="disabled"/>
				<label for="date_days">days</label>
			</p>
			<p>
				<input id="date_spec" type="radio" name="js_date" checked="checked"/>
				<label for="date_spec">between</label>
				<input id="date_spec_text1" type="text" name="go_start" value="today"/>
				<label for="date_spec">and</label>
				<input id="date_spec_text2" type="text" name="go_end" value="today +7 days"/>
			</p>
		</div>
	<h2>Goal Specifics</h2>
		<p class="notice">Specify the conditions of your goal.</p>
		<p>I would like to run <input type="text" name="go_metadata[dist_tot]" size="6" /> miles.</p>
		<p>I would like to run at an average pace of <input type="text" name="go_metadata[pace_avg]" size="6" /> miles/hour.</p>
		<p>I would like to run for <input id="input_time" type="text" name="go_metadata[time_tot]" size="6" /> minutes.</p>
		<p><input type="submit" value="Create"></p>
</div>
</form>
<div class="clear"></div>

<script type="text/javascript">
Date.prototype.toShortForm = function(){
	return (this.getMonth()+1) + "/" + this.getDate() + "/" + this.getFullYear();
}

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
			$("#date_spec_text1").removeAttr("disabled");
			$("#date_spec_text2").removeAttr("disabled");
			form.submit();
		}
	});
	var date_handlers = {
		"date_month": function(){
			var start = new Date();
			var end = new Date();
			start.setDate(1);
			end.setMonth(start.getMonth() + 1);
			end.setDate(1);
			return {start:start, end:end};
		},
		"date_week": function(){
			var start = new Date();
			var end = new Date();
			end.setDate(end.getDate()+7);
			return {start:start, end:end};
		},
		"date_year": function(){
			var start = new Date();
			var end = new Date();
			start.setDate(1);
			start.setMonth(0);
			end.setDate(1);
			end.setMonth(0);
			end.setYear(end.getFullYear() + 1);
			return {start:start, end:end};
		},
		"date_days": function(){
			var ref = $("#date_days_text");
	
			if(!parseFloat($(ref).val())) $(ref).val(7);
	
			var start = new Date();
			var end = new Date();	
			end.setDate(end.getDate() +  parseInt($(ref).val()));
			return {start:start, end:end};
		}
	};
	$("#date_days_text").blur(function(){
		var date = date_handlers["date_days"]();
		$("#date_spec_text1").val(date.start.toShortForm());
		$("#date_spec_text2").val(date.end.toShortForm());
	});
	
	$("label, input:radio").click(function(){
		var inputs = $("#date_section input:text").val("").attr("disabled", "disabled");
		var elem = $(this).is("label")? $(this).attr("for"): $(this).attr("id");
		$("#" + elem).attr("checked", "checked");
		var input = $("#date_section input[id*="+elem+"_text]").removeAttr("disabled");
		input.eq(0).focus();

		if(date_handlers[elem]){
			var date = date_handlers[elem]();
			$("#date_spec_text1").val(date.start.toShortForm());
			$("#date_spec_text2").val(date.end.toShortForm());
		}
		if($(this).is("label")) return false;
		return true;
	});
});
</script>