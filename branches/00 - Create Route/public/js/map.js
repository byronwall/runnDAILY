var map;
var route_points =[];
var route_line;
var total_distance = 0;
var meters_to_miles = 0.000621371192;
var isRouteLineInit = true;
var line_color = "#000000";

var tinyIcon = new GIcon();
tinyIcon.image = "/img/dot.png";
tinyIcon.shadow = "";
tinyIcon.iconSize = new GSize(12, 12);
tinyIcon.shadowSize = new GSize(0, 0);
tinyIcon.iconAnchor = new GPoint(6, 6);
tinyIcon.infoWindowAnchor = new GPoint(6, 6);

var map_options = new Object();
map_options.draggable = true;

var user_options = new Object();
user_options.latlng_start = null;

/*
 * marker_id
 * 
 * This property is added to all markers because it is used for updating the
 * internal references when markers are dragged around.
 * 
 */
GMarker.prototype.marker_id = -1;

/******************************************************************************
 * This section is used to define custom types and their properties
 *****************************************************************************/

/*
 * routePoint
 * 
 * This type is used to hold the data for the current points on the route.
 * There is a reason that a custom type was created, but I fotet.  This
 * will be updated.
 */
function routePoint(){
	
}
routePoint.prototype = {
	latlng:null,
	marker:null	
}

/******************************************************************************
 * This section contains the functions that make the map go.
 *****************************************************************************/
 
 /*
  * load
  * 
  * The load function is called after the document has loaded.
  * This function is used to initialize the map including the 
  * map types displayed, the central location, and also the event
  * handlers.
  */
function load(map_holder_id, click_callback) {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById(map_holder_id), {mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP,G_PHYSICAL_MAP]});
		map.setCenter(new GLatLng(38.4242126,-86.930522), 13);
		
		if(click_callback != null){
			GEvent.addListener(map,"click", click_callback);
		}
		map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
		map.enableScrollWheelZoom();
	}  
}

/*
 * map_click
 * 
 * -overlay, if an overlay were clicked on (point or line) this parameter
 * will contain a reference
 * 
 * -latlng, this is a GLatLng that contains the location of the click
 * 
 * This function is the event handler for click events on the map.  The
 * event handling is done in the load function.  All it really serves to 
 * do is filter out clicks that are not new points and then adds that new
 * point to the map.
 */
function map_click(overlay, latlng, overlaylatlng){
	if(overlay){				
		addPoint(overlaylatlng);
	}
	if (latlng) {		
		addPoint(latlng);		
	}
}

/*
 * update_distance
 * 
 * This function is called to updated the element of the interface that
 * contains the total distance.  This function will soon be deprecated
 * by a more universal refresh function.
 */
function updateRouteDistanceDisp(){
	//var route_length = (route_line.getLength()*meters_to_miles).toFixed(2);
	$("#r_distance_disp").text(total_distance.toFixed(2));
}

var geocoder = new GClientGeocoder();

/*
 * show_address
 * 
 * -address, this is a string representing the address to move the map to.
 * 
 * This function asks Google to figure out the point that the user is talking
 * about.  It then calls the callback which updates the map.
 */
function show_address(address) {
  geocoder.getLatLng(address,show_address_callback );
}

/*
 * show_address_callback
 * 
 * -point, GLatLng containing the new location for the map
 * 
 * This is the callback for the show_address funciton.  When the point has
 * been determined, this function recenters the map.
 */
function show_address_callback(point){
	var location_msg = $("#location_msg");
	if (!point) {
		location_msg.text("The location you entered could not be found.");
		location_msg.removeClass("success");
		location_msg.addClass("error");
		
      } 
	else {
		location_msg.text("The map has been re-centered.");
		location_msg.removeClass("error");
		location_msg.addClass("success");
        map.setCenter(point, 13);
    }
}

/*
 * convertToPolyline
 * 
 * This function is used to convert the current route to the encoded polyline
 * string that represents the route.  This function is to be used before
 * saving a route to the database.  It should probably not be called directly
 * unless for that purpose.
 */
function convertToPolyline(){
	var encoder = new PolylineEncoder();
	var enoder_output = encoder.dpEncodeToJSON(
		$.map(
			route_points,
			function(route_point){
				return route_point.latlng;
			}
		)
	);
	
	return $.toJSON({points: enoder_output.points, levels: enoder_output.levels});
}

/*
 * saveSubmit
 * 
 * -submitForm, reference to the form that contains the submission info
 * 
 * This function is called in the onSubmit section of a form.  It is used
 * to populate the hidden fields that correspond to the route data that
 * needs to be sent to the save route page.
 */
function saveSubmit(form){
	form.r_points.value = convertToPolyline();
	form.r_start_lat.value = route_points[0].latlng.lat();
	form.r_start_lng.value = route_points[0].latlng.lng();
	form.r_distance.value = total_distance.toFixed(2);

	return true;
}

/*
 * _markerDragEnd
 * 
 * -latlng, the new GLatLng for the displaced marker
 * 
 * This function is the event callback for when markers are dragged around. It
 * updates the location of the marker and refreshes everything.
 * 
 * The this reference is the marker that triggered the event.
 * 
 */
function _markerDragEnd(latlng_new){
	route_points[this.marker_id + 1].latlng = latlng_new;
	
	map_refreshAll();
}

/*
 * map_refreshAll
 * 
 * Function which completely refreshes the map.
 * This is done by clearing all overlays, adding markers again,
 * adding the route line back in, updating mile markers, and
 * finally updating the distance.
 * 
 */
function map_refreshAll(){
	map.clearOverlays();
	
	$.each(
		route_points, 
		function(index, route_point){
			map.addOverlay(route_point.marker);	
		}
	);
	route_line = new GPolyline(
		$.map(
			route_points,
			function(route_point){
				return route_point.latlng;
			}
		),
		line_color,
		3
	);
	map.addOverlay(route_line);
	total_distance = route_line.getLength() * meters_to_miles;
	
	updateMileMarkers(true);
	drawMileCircle();
	updateRouteDistanceDisp();
}

/*
 * addPoint
 * 
 * -latlngNew, GLatLng for the point that needs to be added
 * 
 * This function adds a point to the map and the current route.  The point
 * is added as the next one in order.  This function is currently called
 * from the click event handler and also the out and back function.  It
 * can be called from anywhere that needs to add a point to the route.
 */
function addPoint(latlng_new){
	var markerOptions = { icon:tinyIcon, draggable:map_options.draggable };
	var marker_new = new GMarker(latlng_new, markerOptions);
	map.addOverlay(marker_new);
	
	if(markerOptions.draggable){
		GEvent.addListener(marker_new, "dragend", _markerDragEnd);
	}
	
	if(!isRouteLineInit){
		var i = route_points.length;
		route_line.insertVertex(route_points.length, latlng_new);
		if (i > 0){
			total_distance += route_points[i-1].latlng.distanceFrom(latlng_new) * meters_to_miles;
			updateRouteDistanceDisp();
		}
	}
	else{
		route_line = new GPolyline([latlng_new], line_color, 3, .5);
		map.addOverlay(route_line);
		isRouteLineInit = false;
	}
	
	var point = new routePoint();
	point.latlng = latlng_new;
	point.marker = marker_new;
	
	point.marker.marker_id = route_points.length - 1;
	route_points.push(point);
	
	updateMileMarkers(false);
	drawMileCircle();
}

/******************************************************************************
 * This section pertains to the addition of mile markers.
 *****************************************************************************/
var previousDistance = 0;
var mileDistance = 1.0;
var shouldUpdateAll;
var previousMarkerDistance =0;
var mileMarkers = [];

/*
 * updateMileMarkers
 * 
 * -shouldUpdateAll, boolean that determines whether all the markers are cleared
 * and added again, or if just the new markers are added.  If only a single point
 * has been added, use false so that only new markers are determined.
 * 
 * This function is used to determine where mile markers should appear.  It then
 * adds the markers to the map in the correct places.  This function is designed
 * to be able to only add new markers as required, or to also redraw all of them.
 */
function updateMileMarkers(shouldUpdateAll){
	if(route_points.length <= 1){
		return;	
	}
	var temp_total = previousDistance;
	var indexStart = route_points.length - 1;
	
	if(shouldUpdateAll){
	//this is the code to start at the front and redo all the markers
		for(var i = mileMarkers.length-1; i >= 0; i--){
			point = mileMarkers.pop();
			map.removeOverlay(point.marker);
		}
		previousMarkerDistance = 0;
		previousDistance = 0;
		indexStart = 1;
		temp_total = 0;
	}
	
	for(var j = indexStart;j < route_points.length;j++){			
		var curLatLng = route_points[j].latlng;
		var prevLatLng = route_points[j - 1].latlng;
		
		temp_total += prevLatLng.distanceFrom(curLatLng) * meters_to_miles;
		
		var distIntoSec = previousDistance - Math.floor(previousDistance / mileDistance) * mileDistance; 
	
		for(var i =1; i< (temp_total - previousMarkerDistance) / mileDistance;i++){	
			
			var scale = (mileDistance * i - distIntoSec) / (temp_total - previousDistance); 
			
			var lat = prevLatLng.lat() + scale * (curLatLng.lat() - prevLatLng.lat());
			var lng = prevLatLng.lng() + scale * (curLatLng.lng() - prevLatLng.lng());
			
			addMileMarker(lat, lng);			
		}
		
		previousMarkerDistance =  Math.floor(temp_total / mileDistance) * mileDistance;
		previousDistance = temp_total;	
	}
}

var mile_marker_icon = new GIcon();
mile_marker_icon.image = "/img/pin.png";
mile_marker_icon.shadow = "";
mile_marker_icon.iconSize = new GSize(16, 16);
mile_marker_icon.shadowSize = new GSize(0, 0);
mile_marker_icon.iconAnchor = new GPoint(0, 14);
mile_marker_icon.infoWindowAnchor = new GPoint(0, 14);
var mile_marker_options = {icon: mile_marker_icon, clickable: false};

/*
 * addMileMarker
 * 
 * -lat, latitude for the new marker
 * -lng, longitude for the new marker
 * 
 * This function is a helper function for the updateMileMarker function.
 * It is used to add the GMarker to the map where required.  It should 
 * not be called from any place other than the updateMileMarker function.
 */
function addMileMarker(lat, lng){
	var latlng = new GLatLng(lat, lng);
	var marker = new GMarker(latlng, mile_marker_options);
	
	var point = new routePoint();
	point.latlng = latlng;
	point.marker = marker;
	
	map.addOverlay(marker);
	mileMarkers.push(point);
}

/*
 * 
 */
 var circle_distance = 5.0;
 var circle_poly;
 var circle_show = false;
 var circle_init = false;
 var circle_points = 18;
 
 function drawMileCircle(){
 	if(circle_show){
 		if(circle_init){
 			map.removeOverlay(circle_poly);
 		}
 		var circle_rad = circle_distance - total_distance;
	 	if(circle_rad > 0 && route_points.length > 0){
	 		var center = route_points[route_points.length - 1].latlng;
	 		var latConv = center.distanceFrom(new GLatLng(center.lat()+0.1,center.lng()))/160.939;
	        var lngConv = center.distanceFrom(new GLatLng(center.lat(),	center.lng()+0.1))/160.939;
	 		
	 		var circle_latlng = [];
	 		for(var i = 0;i<=circle_points;i++){
	 			var angle = 2 * Math.PI / circle_points * i;
	 			circle_latlng[i] = new GLatLng(center.lat() + Math.cos(angle)*circle_rad/latConv, center.lng() + Math.sin(angle)*circle_rad/lngConv);
	 		}
	 		circle_poly = new GPolygon(circle_latlng, "#444444", 1, 0.5, "#555555", 0.2);
	 		map.addOverlay(circle_poly);
	 		circle_init = true;
	 		
	 		return true;
	 	}
 	}
 	if(circle_init){
 		map.removeOverlay(circle_poly);
 		circle_init = false;
 	} 	
 }


/******************************************************************************
 * This final section pertains to the additional interaciton on the map.
 *****************************************************************************/
 
 /*
  * undoLastPoint
  * 
  * This function is used to remove the last point from the map.
  */
function undoLastPoint(){
	route_points.pop();	
	map_refreshAll();
}

/*
 * clearAllPoints
 * 
 * This function is used to remove all points from the map.
 */
function clearAllPoints(){
	route_points = [];
	isRouteLineInit = true;
	map_refreshAll();
}

/*
 * outAndBack
 * 
 * This function is used to mirror the current route.
 */
function outAndBack(){
	for(var i = route_points.length-1;i>=0;i--){
		addPoint(route_points[i].latlng);
	}	
}

function loadRouteFromDB(polyline_options, is_edit){
	clearAllPoints();
	map_options.draggable = is_edit;
	polyline_options.zoomFactor = 2;
	polyline_options.numLevels=18;
	var polyline = new GPolyline.fromEncoded(polyline_options);

	var boundingBox = polyline.getBounds();
	map.setCenter(boundingBox.getCenter(), map.getBoundsZoomLevel(boundingBox));
	for(var k = 0; k < polyline.getVertexCount(); k++){
		var latlng = polyline.getVertex(k);
		addPoint(latlng);
	}
}