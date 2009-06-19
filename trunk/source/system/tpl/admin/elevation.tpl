<div class="grid_12">
	<h2 id="page-heading">Elevation Control Panel</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<div class="actions">
		<a href="#elevation_list" class="facebox icon"><img src="/img/icon/help.png"/>Show existing list.</a>
		<a href="/admin/ajax_elevation_routes" class="ajax icon" rel="elevation"><img src="/img/icon/help.png"/>Get (50) routes without elevation</a>
		<a href="/admin/ajax_elevation_update_all" class="confirm icon" rel="elevation"><img src="/img/icon/help.png"/>Update elevation for (10) routes</a>
	</div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Step 1: New data</h4>
	<p>
		Click where you want more data.  Right now the boxes of new data are the same size.
		This corresponds to around 15,000 points of elevation for a given area.  This size is 
		necessary to make lookups quick.
	</p>
	<p id="elevation_box"><a href="#elevation_modal" class="facebox">Download Data</a></p>
	
	<div id="routes"></div>
	<div>
		<form>
			<p><input type="radio" name="select_type" value="one" checked="checked"/>Draw entire box on single click</p>
			<p><input type="radio" name="select_type" value="two" />Two clicks to define corners</p>
		</form>
	</div>
	<div class="map" id="map"></div>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Step 2: Pack elevation data LOCALLY</h4>
	<form action="/admin/action_elevation_pack" method="post" enctype="multipart/form-data" size="10000000">
		<p>Select large HDR file</p><input type="file" name="large_hdr"/>
		<p>Select large FLT file</p><input type="file" name="large_flt"/>
		<p><input type="submit" value="create packed file"/></p>
	</form>
</div>
<div class="clear"></div>

<div class="grid_12">
	<h4>Step 3: Add elevation data to server</h4>
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
<div id="elevation_list" class="modal">
		<p class="notice">Select to zoom in.</p>
</div>

{{include file=routes/parts/script.tpl}}
<script type="text/javascript">
$(function(){
	var callbacks = {
		"elevation" : function(data, link){
			$(link).hide();
			var template = '<p><a href="#" rel={id}>{name}</a></p>';
			var html = "";
			$.each(data, function(){
				//avoid empty data
				if(!this.points) return;
				this.points = $.parseJSON(this.points);
				this.points.zoomFactor = 2;
				this.points.numLevels=18;
				//avoid empty data
				if(!this.points.points) return;
				var marker = GPolyline.fromEncoded(this.points);
				var in_bounds = false;
				$.each(bounds, function(){
					//make sure that the route doesn't fit anywhere
					if(this.containsBounds(marker.getBounds())){
						in_bounds = true;
						return false;
					}
				});
				if(!in_bounds){
					Map.instance.addOverlay(marker);
					html += $.template(template, this, true);
				}
			});
			$.facebox(html, 3000);
		},
		"update_all" : function(data){
			var html = "Just to let you know it worked.";
			$.each(data, function(){
				html += "<p>"+this+"</p>";
			});
			$.facebox(html, 2000);
		}
	};
	//confirm pulls up a box with a link to an AJAX link
	$("a.confirm").click(function(){
		var html = '<a href="{href}" class="ajax" rel="update_all"><p>Please click to follow link to {href}.</p></a>';
		$.facebox($.template(html, this), 10000);
		return false;
	});
	//ajax goes through an AJAX get and expects a form response
	$("a.ajax").live("click", function(){
		var link = this;
		$.facebox.loading();
		$.get(this.href, {}, function(data){
			$.facebox.close();
			if(!data.result){
				$.facebox("Error on AJAX.", 1000);
				return false;
			}
			if(callbacks[link.rel]) callbacks[link.rel](data.data, link)
			else{
				$.facebox("Request was executed.", 2000);
			}
		}, "json");
		return false;
	});
	$("a.elevation_zoom").live("click", function(){
		$.facebox.close();
		var id = this.rel;
		var shape = elevation[id].shape;
		Map.instance.setCenter(shape.getBounds().getCenter(), 9);
		return false;
	});
	$("input:radio[name=select_type]").change(function(){
		Map.changeCallback(click_callbacks[$(this).val()]);
	});
	var click_callbacks = {
		"one": function(overlay, latlng){
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
	
			updateLink(poly.getBounds());
		},
		"two": function(overlay, latlng){
			//click inside overlay is not allowed
			if(!latlng) return
			//first point is saved
			if(!this.latlng){
				this.latlng = latlng;
				return;
			}
			
			//clockwise building of rectanlge
			var rect = [
		   		latlng,
		   		new GLatLng(latlng.lat(), this.latlng.lng()),
		   		new GLatLng(this.latlng.lat(), this.latlng.lng()),
		   		new GLatLng(this.latlng.lat(), latlng.lng()),
		   		latlng
	   		];

			if(poly) Map.instance.removeOverlay(poly);
			poly = new GPolygon(rect);
			var size = poly.getBounds();		
			$.each(bounds, function(){
				if(this.intersects(poly.getBounds())){
					poly.error = true;
					$.facebox("Cannot intersect existing", 500);
					return false;
				}
			});

			this.latlng = null;
			//error means there is intersecting
			if(poly.error) return;
			if(size.toSpan().lat()*size.toSpan().lng() > max_size){
				$.facebox("select a smaller area", 500);
				return;
			}
			
			Map.instance.addOverlay(poly);
			Map.instance.setCenter(poly.getBounds().getCenter(), 8);
			updateLink(poly.getBounds());

		}
	}
	function updateLink(bounds){
		var sw = bounds.getSouthWest();
		var ne = bounds.getNorthEast();

		var lat_top = ne.lat();
		var lat_bottom = sw.lat();
		var lng_left = sw.lng();
		var lng_right = ne.lng();

		var link = $("#elevation_box a").get(0);
		
		link.href="http://extract.cr.usgs.gov/Website/distreq/RequestSummary.jsp?PR=0&CU=Native&ZX=-1.0&ZY=-1.0&ML=COM&MD=DL&AL="
		link.href += lat_top + "," + lat_bottom + "," + lng_right + "," + lng_left;
		link.href += "&CS=250&PL=NED05HZ";
	}
	//these are all the regions
	var elevation = {{$elevation}};
	var bounds = [];
	Map.load("map", click_callbacks["one"]);

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
		bounds.push(this.shape.getBounds());

		var html = "<p><a href='#' class='elevation_zoom' rel='{id}'>{description}</a></p>"; 
		$("#elevation_list").append($.template(html, this));
	});

	var max_size = 0.043866899999997246 * 1.5;
	var cell_size = 0.00028;
	var skip = 6;
	var points = 15625;
	var poly;
});

</script>