<div class="grid_12">
<h2 id="page-heading">Browse All Routes</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
<ul id="errors"></ul>
<form id="route_browse_form" action="/routes/browse" method="get">
		<p><label>Username: </label><input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></p>
		<p>
			<label>Distance: </lablel>
			<input type="text" name="r_distance[0]" value="{{$smarty.get.r_distance[0]}}"/> to <input type="text" name="r_distance[1]" value="{{$smarty.get.r_distance[1]}}" /> miles
		</p>
		<p><label>Route Name: </label><input type="text" name="r_name" value="{{$smarty.get.r_name}}"/></p>
		<p>
			<label>Date Created: </label>
			<input type="text" name="r_creation[0]" value="{{$smarty.get.r_creation[0]}}"/> to <input type="text" name="r_creation[1]" value="{{$smarty.get.r_creation[1]}}"/>
		</p>
		<p><input type="submit" value="Browse"/></p>
</form>
</div>
<div class="clear"></div>

<div class="grid_12">
	{{include file="routes/parts/route_list.tpl" routes=$routes query=$query }}
</div>
<div class="clear"></div>

<script type="text/javascript">

var anchorCall;
$(document).ready( function(){
	prep_ajax($("a.ajax"));
	$("#route_browse_form").validate({
		rules: {
			"r_distance[0]":{
				number:true
			},
			"r_distance[1]":{
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