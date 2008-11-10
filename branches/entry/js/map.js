var map;
var route_points =[];
var route_line;
var total_distance = 0;
var meters_to_miles = 0.000621371192;

/*
 * This section is used to define custom types and their properties
 * 
 */

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
	marker:null,
	index:0	
}

/*
 * This section contains the functions that make the map go.
 */
 
 /*
  * load
  * 
  * The load function is called after the document has loaded.
  * This function is used to initialize the map including the 
  * map types displayed, the central location, and also the event
  * handlers.
  */
function load() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"), {mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP,G_PHYSICAL_MAP]});
		map.setCenter(new GLatLng(40.4242126,-86.930522), 13);
		GEvent.addListener(map,"click", map_click);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.enableScrollWheelZoom();
		
		//This definition does not belong in here and will be moved.
		var tinyIcon = new GIcon();
		tinyIcon.image = "images/marker.png";
		tinyIcon.shadow = "";
		tinyIcon.iconSize = new GSize(7, 7);
		tinyIcon.shadowSize = new GSize(0, 0);
		tinyIcon.iconAnchor = new GPoint(4, 4);
		tinyIcon.infoWindowAnchor = new GPoint(5, 1);

		markerOptions = { icon:tinyIcon };
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
function map_click(overlay, latlng){
	if(overlay){				
		return;
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
function update_distance(){
	var display = document.getElementById("distance_holder");
	display.innerHTML = total_distance.toFixed(2);	
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
	if (!point) {
        alert("not found");
      } 
	else {
        map.setCenter(point, 13);
    }
}

/*
 * NOTE: I have removed the fullscreen stuff.  It did not really work and
 * can be added back when it is required.
 */

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
	line = encoder.dpEncodeToJSON(_getLatLngArray( route_points));
	
	return $.toJSON(line);

}

/*
 * _getLatLngArray
 * 
 * -routePointArray, array of routePoint's that need to be transformed to GLatLng
 * 
 * This function is used to create a new array of GLatLng from routePoint.
 * routePoint already has the GLatLng in it.  This is solely a helper function
 * that is used for the encode polyline algorithm.  Probably not going to be
 * called directly.
 */
function _getLatLngArray(routePointArray){
	var output = [];
	for(var i =0;i<routePointArray.length;i++){
		output[i] = routePointArray[i].latlng;
	}
	return output;
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
function saveSubmit(submitForm){
	submitForm.distance.value = total_distance;
	submitForm.comments.value = "some comments";
	submitForm.points.value = convertToPolyline();

	return true;
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
function addPoint(latlngNew){
	var markerNew = new GMarker(latlngNew, markerOptions);
	map.addOverlay(markerNew);
	
	if(!isRouteLineInit){
		route_line = new GPolyline([latlngNew],"#ff0000", 3);
		map.addOverlay(route_line);
		isRouteLineInit = true;
	}
	else{
		route_line.insertVertex(route_line.getVertexCount(), latlngNew);
		total_distance += latlngNew.distanceFrom(route_points[route_points.length-1].latlng) * meters_to_miles;
		update_distance();
	}
	
	var point = new routePoint();
	point.latlng = latlngNew;
	point.marker=markerNew;
	point.index=route_points.length - 1;
	
	route_points.push(point);
	
	updateMileMarkers(false);
}

/*
 * This section pertains to the addition of mile markers.
 */
var previousDistance = 0;
var mileDistance = 1.0;
var shouldUpdateAll;
var previousMarkerDistance =0;
var isRouteLineInit = false;
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
		for(var i =mileMarkers.length-1;i>=0;i--){
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
	var markerIcon = new GIcon();
	markerIcon.image = "images/mile-marker.png";
	markerIcon.shadow = "";
	markerIcon.iconSize = new GSize(28, 35);
	markerIcon.shadowSize = new GSize(0, 0);
	markerIcon.iconAnchor = new GPoint(0, 35);
	markerIcon.infoWindowAnchor = new GPoint(0, 35);
	var options = {icon: markerIcon};
	
	var latlng = new GLatLng(lat, lng);
	var marker = new GMarker(latlng, options);
	
	var point = new routePoint();
	point.latlng = latlng;
	point.marker = marker;
	
	map.addOverlay(marker);
	mileMarkers.push(point);
}

/*
 * This final section pertains to the additional interaciton on the map.
 */
 
 /*
  * undoLastPoint
  * 
  * This function is used to remove the last point from the map.
  */
function undoLastPoint(){
	var point = route_points.pop();
	route_line.deleteVertex(route_line.getVertexCount()- 1);		
	map.removeOverlay(point.marker);
	updateMileMarkers(true);
}

/*
 * clearAllPoints
 * 
 * This function is used to remove all points from the map.
 */
function clearAllPoints(){
	map.clearOverlays();
	route_points = [];
	mileMarkers = [];
	total_distance = 0;
	previousDistance = 0;
	previousMarkerDistance = 0;
	isRouteLineInit = false;
	
	update_distance();	
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
