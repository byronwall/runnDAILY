<div class="grid_12">
	<h2 id="page-heading">routes / road_test</h2>
</div>

<div class="clear"></div>

<div class="grid_12">
<div id="results" style="display:none"></div>
<div id="map" class="map large"></div>

</div>
<div class="clear"></div>

{{include file="routes/parts/script.tpl"}}

<script type="text/javascript">
$(document).ready(function(){
	Directions.init();
	load("map", Directions.click);
});

</script>