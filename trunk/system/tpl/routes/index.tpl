<div class="grid_12">
<h2 id="page-heading">Routes</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
<div class="actions">
	<a href="/routes/create" class="icon"><img src="/img/icon_route_plus.png"/>New Route</a>
	<a href="/routes/browse" class="icon"><img src="/img/icon_cards_stack.png"/>Browse Routes</a>
</div>
</div>
<div class="clear"></div>

{{modules list=$currentUser->settings.routes_modules}}
<div class="clear"></div>

<div class="clear"></div>
<div style="display: none" id="preview_map">
<div id="map_placeholder" class="map large"></div>
</div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script type="text/javascript">
var init = false;
$(document).ready(function(){
	$("a.preview").click( function(){
		if(!init){
			Map.load("map_placeholder", null);
			init = !init;
		}			
		tb_show(this.title, this.href, false);
		Map.instance.checkResize();
		MapData.loadRoute($.parseJSON(this.rel));
		
		return false;
	});
}
);

</script>