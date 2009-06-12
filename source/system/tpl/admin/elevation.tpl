<div class="grid_12">
	<h2 id="page-heading">Elevation Control Panel</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h1>Step 1: New data</h1>
	<p>
		Click where you want more data.  Right now the boxes of new data are the same size.
		This corresponds to around 15,000 points of elevation for a given area.  This size is 
		necessary to make lookups quick.
	</p>
	<p>Green = existing, Blue = new</p>
	<a href="#elevation_list" class="facebox">Show existing list.</a>
	<div id="elevation_list" class="modal">
		<p class="notice">Select to zoom in.</p>
	</div>
	<p id="elevation_box"><a href="#elevation_modal" class="facebox">Download Data</a></p>
	
	<div class="map" id="map"></div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h1>Step 2: Pack elevation data LOCALLY</h1>
	<form action="/admin/action_elevation_pack" method="post" enctype="multipart/form-data" size="10000000">
		<p>Select large HDR file</p><input type="file" name="large_hdr"/>
		<p>Select large FLT file</p><input type="file" name="large_flt"/>
		<p><input type="submit" value="create packed file"/></p>
	</form>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h1>Step 3: Add elevation data to server</h1>
	<form action="/admin/action_elevation_add_packed" method="post" enctype="multipart/form-data">
		<p>Describe this data set.<input type="text" name="region_name" value="Elevation data"/></p>
		<p>Select packed HDR file</p><input type="file" name="packed_hdr"/>
		<p>Select packed FLT file</p><input type="file" name="packed_flt"/>
		<p><input type="submit" value="add data to database"/></p>
	</form>
</div>
<div class="clear"></div>

<div id="elevation_modal" class="modal">
	<p>Click on the map to get new data.</p>
</div>

{{include file=routes/parts/script.tpl}}
<script type="text/javascript">
$(function(){
	$("a.elevation_zoom").live("click", function(){
		$.facebox.close();
		var id = this.rel;
		var shape = elevation[id].shape;
		Map.instance.setCenter(shape.getBounds().getCenter(), 9);
		return false;
	});
	
	var elevation = {{$elevation}};
	Map.load("map", click_call);

	$.each(elevation, function(){
		var rect = [
		    		new GLatLng(this.lat_nw, this.lng_nw),
		    		new GLatLng(this.lat_se, this.lng_nw),
		    		new GLatLng(this.lat_se, this.lng_se),
		    		new GLatLng(this.lat_nw, this.lng_se),
		    		new GLatLng(this.lat_nw, this.lng_nw)
		    		]
		this.shape = new GPolygon(rect, "#2FB81B", 2, 1, "#85FF00");
		Map.instance.addOverlay(this.shape);

		var html = "<p><a href='#' class='elevation_zoom' rel='{id}'>{description}</a></p>"; 
		$("#elevation_list").append($.template(html, this));
	});

	var cell_size = 0.00028;
	var skip = 6;
	var points = 15625;

	var poly;
	
	function click_call(overlay, latlng){
		if(!latlng) return;

		
		var block = Math.sqrt(points) * cell_size * skip;

		latlng = new GLatLng(latlng.lat() - block / 2, latlng.lng() - block / 2);
		lat_sw = latlng.lat();
		lng_sw = latlng.lng();

		//clockwise building of rectanlge
		var rect = [
	   		latlng,
	   		new GLatLng(lat_sw + block, lng_sw),
	   		new GLatLng(lat_sw + block, lng_sw + block),
	   		new GLatLng(lat_sw, lng_sw + block),
	   		latlng
   		];
		if(poly) Map.instance.removeOverlay(poly);
		poly = new GPolygon(rect);		
		Map.instance.addOverlay(poly);
		Map.instance.setCenter(poly.getBounds().getCenter(), 8);

		$("#elevation_box a").each(function(){
			var bounds = poly.getBounds();
			var sw = bounds.getSouthWest();
			var ne = bounds.getNorthEast();

			var lat_top = ne.lat();
			var lat_bottom = sw.lat();
			var lng_left = sw.lng();
			var lng_right = ne.lng();
			
			this.href="http://extract.cr.usgs.gov/Website/distreq/RequestSummary.jsp?PR=0&CU=Native&ZX=-1.0&ZY=-1.0&ML=COM&MD=DL&AL="
			this.href += lat_top + "," + lat_bottom + "," + lng_right + "," + lng_left;
			this.href += "&CS=250&PL=NED05HZ";
		});
	}
	
});

</script>