<div class="grid_12">
<h2 id="page-heading">Browse All Training Entries</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
<ul id="errors"></ul>

<form id="training_browse_form" action="/training/browse" method="get">
		<p><label>Username: </label><input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></p>
		<p>
			<label>Distance: </label> 
			<input type="text" name="t_distance[0]" value="{{$smarty.get.t_distance[0]}}"/> to <input type="text" name="t_distance[1]" value="{{$smarty.get.t_distance[1]}}" /> miles
		</p>
		<p>
			<label>Time: </label>
			<input type="text" name="t_time[0]" value="{{$smarty.get.t_time[0]}}"/> to <input type="text" name="t_time[1]" value="{{$smarty.get.t_time[1]}}"/>
		</p>
		<p>
			<label>Date Created: </label>
			<input type="text" name="t_date[0]" value="{{$smarty.get.t_date[0]}}"/> to <input type="text" name="t_date[1]" value="{{$smarty.get.t_date[1]}}"/>
		</p>
		<p>
			<input type="submit" value="Browse"/>
			<input type="button" class="cancel" value="Cancel"/>
			<input type="button" class="reset" value="Clear All"/>
		</p>
</form>
</div>
<div class="clear"></div>

<div class="grid_12">
	{{include file="training/parts/item_list.tpl"}}
</div>
<div class="clear"></div>

<script type="text/javascript">

var anchorCall;
$(document).ready( function(){
	prep_ajax($("a.ajax"));
	validator = $("#training_browse_form").validate({
		rules: {
			"t_distance[0]":{
				number:true
			},
			"t_distance[1]":{
				number:true
			}
		},
		submitHandler: function(form){
			$("input[type=text]").each( function(){
				if($(this).val() == "") $(this).attr("disabled", true);
			});
			form.submit();
		},
		errorLabelContainer: "#errors",
		wrapper: "li"
	});
	$("input.cancel").click( function(){
		validator.resetForm();
	});
	$("input.reset").click( function(){
		validator.resetForm();
		$("#training_browse_form").clearForm();
	});
});

function prep_ajax(DOM){
	DOM.click( function(){
		anchorCall = $(this).parent().before("<li><img src='/img/loadingAnimation.gif' /></li>");
		anchorCall.fadeOut("slow");
 		$.get(this.href, function(data){
			anchorCall.prev("li").remove();
			anchorCall.before(data);
			prep_ajax(anchorCall.prev().find("a.ajax"));
			anchorCall.remove();
		}, "html");
		return false;
	});
	
}

</script>