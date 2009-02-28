<div class="grid_12">
	<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div class="actions">
		<a href="/routes/create"><img class="icon" src="/img/icon.png" />New Route</a>
	</div>
</div>
<div class="clear"></div>

{{modules list=$currentUser->routes_modules}}

<div class="clear"></div>

<div style="display:none" id="preview_map">
	<div id="map_placeholder" class="map large"></div>
</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>    
<script type="text/javascript">
var init = false;
$(document).ready(function(){
	$("a.preview").click( function(){
		if(!init){
			load("map_placeholder", null);
			init = !init;
		}			
		tb_show(this.title, this.href, false);
		map.checkResize();
		loadRouteFromDB($.parseJSON(this.rel));
		
		return false;
	});
}
);

</script>