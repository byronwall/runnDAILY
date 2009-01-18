<h1>Browse Training Logs on Runn Daily</h1>

<ul id="errors"></ul>

<form id="training_browse_form" action="/training/browse.php" method="get">
	<ul>
		<li>username: <input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></li>
		<li>distance: 
			<input type="text" name="t_distance[0]" value="{{$smarty.get.t_distance[0]}}"/>
			<input type="text" name="t_distance[1]" value="{{$smarty.get.t_distance[1]}}" />
		</li>
		<li>time: 
			<input type="text" name="t_time[0]" value="{{$smarty.get.t_time[0]}}"/>
			<input type="text" name="t_time[1]" value="{{$smarty.get.t_time[1]}}"/>
		</li>
		<li>date created: 
			<input type="text" name="t_date[0]" value="{{$smarty.get.t_date[0]}}"/>
			<input type="text" name="t_date[1]" value="{{$smarty.get.t_date[1]}}"/>
		</li>
		<li><input type="submit" value="search"/></li>
		<li><input type="button" class="cancel" value="reset to start"/></li>
		<li><input type="button" class="reset" value="clear all"/></li>
	</ul>
</form>

<ul>
	{{include file="training/parts/item_list.tpl"}}
</ul>

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