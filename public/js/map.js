var MapActions = {
	outAndBack: function(){
		for(var i = Map.points.length-1;i>=0;i--){
			Map.addPoint(Map.points[i].latlng);
		}	
	},
	undoLastPoint: function(){
		Map.points.pop();	
		Map.refresh();
	},
	clearAllPoints: function(){
		Map.points = [];
		isRouteLineInit = true;
		Map.refresh();
		
	}
}
var MapSettings = {
	DistanceCircle : {
		enable: false,
		radius: 5
	},
	MileMarkers: {
		enable: true,
		distance: 1.0
	},
	Directions: {
		enable: false
	}
}

var Map = {
	config: {
		draggable: true,
		show_points: true
	},
	icon: null,
	iconOptions: null,
	
	instance: null,
	points: [],
	polyline: null,
	
	totalDistance: 0,
	
	init: function(){
		Map.icon = new GIcon();
		Map.icon.image = "/img/dot.png";
		Map.icon.shadow = "";
		Map.icon.iconSize = new GSize(12, 12);
		Map.icon.shadowSize = new GSize(0, 0);
		Map.icon.iconAnchor = new GPoint(6, 6);
		Map.icon.infoWindowAnchor = new GPoint(6, 6);
		
		Map.iconOptions = {
			icon : Map.icon,
			draggable : Map.config.draggable
		}
	},
	load: function(map_holder_id, click_callback, options) {
		if (GBrowserIsCompatible()) {
			if(options && options.full_height){
				$("#"+map_holder_id).heightBrowser();
			}
			Map.instance = new GMap2(document.getElementById(map_holder_id), {mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP,G_PHYSICAL_MAP]});
			Map.instance.setCenter(new GLatLng(38.424212,-86.930522), 13);
			
			if(click_callback != null){
				GEvent.addListener(Map.instance,"click", click_callback);
			}
			Map.instance.addControl(new GSmallMapControl());
			Map.instance.addControl(new GMapTypeControl());
			Map.instance.enableScrollWheelZoom();
			
			Map.instance.savePosition();
			
			MileMarkers.init();
		}
	},
	event_click: function(overlay, latlng, overlaylatlng){
		if(MapSettings.Directions.enable){
			Directions.click(overlay, latlng, overlaylatlng);
		}
		else{
			if(overlay){				
				Map.addPoint(overlaylatlng);
			}
			if (latlng) {		
				Map.addPoint(latlng);		
			}
		}
	},
	addPoint: function(latlng_new){
		if(Map.polyline != null){
			var i = Map.points.length;
			Map.polyline.insertVertex(Map.points.length, latlng_new);
			if (i > 0){
				Map.totalDistance += Map.points[i-1].latlng.distanceFrom(latlng_new) * meters_to_miles;
				Map.updateDistanceDisplay();
			}
		}
		else{
			Map.polyline = new GPolyline([latlng_new], "#000000", 3, .5);
			Map.instance.addOverlay(Map.polyline);
		}
		
		var point = new routePoint();
		point.latlng = latlng_new;

		if(Map.config.show_points){
			var marker_new = new GMarker(latlng_new, Map.iconOptions);
			Map.instance.addOverlay(marker_new);
			point.marker = marker_new;
			point.marker.marker_id = Map.points.length - 1;
			if(Map.iconOptions.draggable){
				$.debug("error");
				GEvent.addListener(marker_new, "dragend", Map.event_dragend);
			}
		}
		else{
			point.marker = null;
		}
		
		
		Map.points.push(point);
		
		MileMarkers.update(false);
		DistanceCircle.draw();
	},
	refresh: function(){
		Map.instance.clearOverlays();
		
		$.each(
			Map.points, 
			function(index, route_point){
				Map.instance.addOverlay(route_point.marker);	
			}
		);
		Map.polyline = new GPolyline(
			$.map(
				Map.points,
				function(route_point){
					return route_point.latlng;
				}
			),
			"#000000",
			3
		);
		Map.instance.addOverlay(Map.polyline);
		Map.totalDistance = Map.polyline.getLength() * meters_to_miles;
		
		MileMarkers.update(true);
		DistanceCircle.draw();
		Map.updateDistanceDisplay();
	},
	updateDistanceDisplay: function(){
		Units.textWithUnits({			dist: Map.totalDistance,			target: ".r_distance_disp"		});	},
	event_dragend: function(latlng_new){
		Map.points[this.marker_id + 1].latlng = latlng_new;
		
		Map.refresh();
	},
	setHomeLocation: function(lat, lng){
		Map.instance.setCenter(new GLatLng(lat, lng), 12);
		Map.instance.savePosition();
	}
}

/*
 * In between these blocks is a bit of code that is yet to be put somewhere.
 */

var meters_to_miles = 0.000621371192;

GMarker.prototype.marker_id = -1;

function routePoint(){
	
}
routePoint.prototype = {
	latlng:null,
	marker:null
}

/*
 * See note above
 */

var Geocoder = {
	instance: new GClientGeocoder(),
	
	showAddress: function(DOM){
		var address = $(DOM).val();
		if(address.toLowerCase() == "home"){
			Map.instance.returnToSavedPosition();
			return;
		}
		Geocoder.instance.getLatLng(address, Geocoder.showAddressCallback);		
	},
	showAddressCallback: function(point){
		var location_msg = $("#location_msg");
		if (!point) {
			location_msg.text("The location you entered could not be found.");
			location_msg.removeClass("success").addClass("error");
		} 
		else {
			location_msg.text("The map has been re-centered.");
			location_msg.removeClass("error").addClass("success");
	        Map.instance.setCenter(point, 13);
		}
	}
}

var MapSave = {
	submitHandler: function(form){
		form.r_points.value = (MapSave.routeToPolyline());
		form.r_start_lat.value = (Map.points[0].latlng.lat());
		form.r_start_lng.value = (Map.points[0].latlng.lng());
		form.r_distance.value = (Map.totalDistance.toFixed(2));
	},
	routeToPolyline: function(){
		var encoder = new PolylineEncoder();
		var enoder_output = encoder.dpEncodeToJSON(
			$.map(
				Map.points,
				function(route_point){
					return route_point.latlng;
				}
			)
		);
		
		return $.toJSON({points: enoder_output.points, levels: enoder_output.levels});
	}
}

var MileMarkers = {
	icon: null,
	icon_options:null,
	points: [],
	prevDistance: 0,
	prevMarkerDistance: 0,
	miles: 0,
	
	init: function(){
		MileMarkers.icon = new GIcon();
		MileMarkers.icon.image = "/img/red_marker.png";
		MileMarkers.icon.shadow = "";
		MileMarkers.icon.iconSize = new GSize(16, 16);
		MileMarkers.icon.shadowSize = new GSize(0, 0);
		MileMarkers.icon.iconAnchor = new GPoint(0, 14);
		MileMarkers.icon.infoWindowAnchor = new GPoint(0, 14);
		
		MileMarkers.icon_options = {
			icon : MileMarkers.icon,
			clickable: false
		}
	},
	add: function(lat, lng, mile){
		var latlng = new GLatLng(lat, lng);
		
		var options = $.extend({}, MileMarkers.icon_options, {
			labelText: mile,
			labelOffset: new GSize(4,-14)
		});
		
		var marker = new LabeledMarker(latlng, options);
		//var marker = new GMarker(latlng, MileMarkers.icon_options);
		
		var point = new routePoint();
		point.latlng = latlng;
		point.marker = marker;
		
		Map.instance.addOverlay(marker);
		MileMarkers.points.push(point);
	},
	update: function(shouldUpdateAll){
		if(Map.points.length <= 1){
			return;	
		}
		var temp_total = MileMarkers.prevDistance;
		var indexStart = Map.points.length - 1;
		
		if(shouldUpdateAll){
			for(var i = MileMarkers.points.length-1; i >= 0; i--){
				point = MileMarkers.points.pop();
				Map.instance.removeOverlay(point.marker);
			}
			MileMarkers.prevMarkerDistance = 0;
			MileMarkers.prevDistance = 0;
			indexStart = 1;
			temp_total = 0;
			MileMarkers.miles = 0;
		}
		
		for(var j = indexStart;j < Map.points.length;j++){			
			var curLatLng = Map.points[j].latlng;
			var prevLatLng = Map.points[j - 1].latlng;
			
			var new_dist = prevLatLng.distanceFrom(curLatLng) * meters_to_miles;
			if(!Units.is_mile){
				new_dist *= Units.convert;
			}
			
			temp_total += new_dist;
			
			var distIntoSec = MileMarkers.prevDistance - Math.floor(MileMarkers.prevDistance / MapSettings.MileMarkers.distance) * MapSettings.MileMarkers.distance; 
		
			for(var i =1; i< (temp_total - MileMarkers.prevMarkerDistance) / MapSettings.MileMarkers.distance;i++){	
				
				var scale = (MapSettings.MileMarkers.distance * i - distIntoSec) / (temp_total - MileMarkers.prevDistance); 
				
				var lat = prevLatLng.lat() + scale * (curLatLng.lat() - prevLatLng.lat());
				var lng = prevLatLng.lng() + scale * (curLatLng.lng() - prevLatLng.lng());
				
				MileMarkers.add(lat, lng, ++MileMarkers.miles);			
			}
			
			MileMarkers.prevMarkerDistance =  Math.floor(temp_total / MapSettings.MileMarkers.distance) * MapSettings.MileMarkers.distance;
			MileMarkers.prevDistance = temp_total;	
		}
	}
}

var DistanceCircle = {
	polyline : null,
	points : 18,
	
	draw : function(){
		if(MapSettings.DistanceCircle.enable){
	 		if(DistanceCircle.polyline != null){
	 			Map.instance.removeOverlay(DistanceCircle.polyline);
	 		}
	 		var circle_rad = MapSettings.DistanceCircle.radius - Map.totalDistance;
		 	if(circle_rad > 0 && Map.points.length > 0){
		 		var center = Map.points[Map.points.length - 1].latlng;
		 		var latConv = center.distanceFrom(new GLatLng(center.lat()+0.1,center.lng()))/160.939;
		        var lngConv = center.distanceFrom(new GLatLng(center.lat(),	center.lng()+0.1))/160.939;
		 		
		 		var circle_latlng = [];
		 		for(var i = 0;i<=DistanceCircle.points;i++){
		 			var angle = 2 * Math.PI / DistanceCircle.points * i;
		 			circle_latlng[i] = new GLatLng(center.lat() + Math.cos(angle)*circle_rad/latConv, center.lng() + Math.sin(angle)*circle_rad/lngConv);
		 		}
		 		DistanceCircle.polyline = new GPolygon(circle_latlng, "#444444", 1, 0.5, "#555555", 0.2);
		 		Map.instance.addOverlay(DistanceCircle.polyline);
		 	}
	 	}
	 	else{
	 		if(DistanceCircle.polyline != null)	Map.instance.removeOverlay(DistanceCircle.polyline);
	 	}
	}	
}

var MapData = {
	loadRoute: function(polyline_options, options){
		MapActions.clearAllPoints();
		Map.config = $.extend({}, Map.config, options);

		Map.init();
		
		polyline_options.zoomFactor = 2;
		polyline_options.numLevels=18;
		var polyline = new GPolyline.fromEncoded(polyline_options);
	
		var boundingBox = polyline.getBounds();
		Map.instance.setCenter(boundingBox.getCenter(), Map.instance.getBoundsZoomLevel(boundingBox));
		for(var k = 0; k < polyline.getVertexCount(); k++){
			var latlng = polyline.getVertex(k);
			Map.addPoint(latlng);
		}
	}
}

var Directions = {
	isSearching: false,
	instance: null,
	polyline: null,
	options: {
		getPolyline: true,
		travelMode: G_TRAVEL_MODE_WALKING,
		avoidHighways: true
	},
	init: function(){
		Directions.instance = new GDirections(null, $("#results")[0]);
		GEvent.addListener(Directions.instance, "load", Directions.load_event); 
		GEvent.addListener(Directions.instance, "error", Directions.error_event);
	},
	click: function(overlay, latlng, overlaylatlng){
		if(Directions.isSearching) return;
		if(latlng){
			if(Map.points.length == 0){
				Map.addPoint(latlng);
			}
			else{
				var prev_point = Map.points[Map.points.length - 1].latlng;
				var str = "from:" + prev_point.lat() + "," + prev_point.lng() + " to: " + latlng.lat() + "," + latlng.lng()
				Directions.isSearching = true;
				Directions.instance.load(str, Directions.options);
			}
		}
	},
	load_event: function(){
		Directions.polyline = Directions.instance.getPolyline();
		var points = Directions.polyline.getVertexCount();
		for(var i = 0; i < points; i++){
			Map.addPoint(Directions.polyline.getVertex(i));
		}
		Directions.isSearching = false;
	},
	error_event: function(){
	}
}
var Display = {
	fullscreen: false,
	toggle_fullscreen: function(){
		if(!Display.fullscreen){
			$("#r_map").removeClass("map").addClass("map_full");
			$("#r_map").css("position", "fixed");
			$("body").css("overflow","hidden");
			$("#map_overlay").show();
			Display.fullscreen = true;
			Map.instance.checkResize();
		}
		else{
			$("#r_map").removeClass("map_full").addClass("map");
			$("#r_map").css("position", "relative");
			$("body").css("overflow","auto");
			$("#map_overlay").hide();
			Display.fullscreen = false;
			Map.instance.checkResize();
		}
	}
}

Map.init();
Directions.init();