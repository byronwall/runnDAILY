<h1>Browse Routes on Runn Daily</h1>

<ul id="errors"></ul>

<form id="route_browse_form" action="/routes/browse.php" method="get">
	<ul>
		<li>username: <input type="text" name="u_username" value="{{$smarty.get.u_username}}"/></li>
		<li>distance: 
			<input type="text" name="r_distance[0]" value="{{$smarty.get.r_distance[0]}}"/>
			<input type="text" name="r_distance[1]" value="{{$smarty.get.r_distance[1]}}" />
		</li>
		<li>route name: <input type="text" name="r_name" value="{{$smarty.get.r_name}}"/></li>
		<li>date created: 
			<input type="text" name="r_creation[0]" value="{{$smarty.get.r_creation[0]}}"/>
			<input type="text" name="r_creation[1]" value="{{$smarty.get.r_creation[1]}}"/>
		</li>
		<li><input type="submit" value="search"/></li>
	</ul>
</form>

<ul>
	{{include file="routes/parts/route_list.tpl" routes=$routes query=$query }}
</ul>

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