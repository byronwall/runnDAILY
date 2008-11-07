var map;
var route_points =[];
var route_line;
var total_distance = 0;
var meters_to_miles = 0.000621371192;

function routePoint(){
	
}
routePoint.prototype = {
	latlng:null,
	marker:null,
	index:0	
}

function load() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"), {mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP,G_PHYSICAL_MAP]});
		map.setCenter(new GLatLng(40.4242126,-86.930522), 13);
		GEvent.addListener(map,"click", map_click);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.enableScrollWheelZoom();
		
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
function map_click(overlay, latlng){
	if(overlay){				
		return;
	}
	if (latlng) {		
		addPoint(latlng);		
	}
}

function update_distance(){
	var display = document.getElementById("distance_holder");
	display.innerHTML = total_distance.toFixed(2);	
}

var geocoder = new GClientGeocoder();

function show_address(address) {
  geocoder.getLatLng(address,show_address_callback );
}
function show_address_callback(point){
	if (!point) {
        alert("not found");
      } 
	else {
        map.setCenter(point, 13);
    }
}
var is_fullscreen = false;
function fullscreen_map(){
	if(is_fullscreen){
		$(".fullscreen").attr("class","map_wrapper");
		$("body").css("overflow", "auto");
		map.checkResize();
	}
	else{
		$(".map_wrapper").attr("class","fullscreen");
		$("body").css("overflow", "hidden");
		map.checkResize();
	}
	is_fullscreen = !is_fullscreen;	
}
function convertToPolyline(){
	var encoder = new PolylineEncoder();
	line = encoder.dpEncodeToJSON(getLatLngArray( route_points));
	
	return $.toJSON(line);

}
function getLatLngArray(routePointArray){
	var output = [];
	for(var i =0;i<routePointArray.length;i++){
		output[i] = routePointArray[i].latlng;
	}
	return output;
}
function saveSubmit(submitForm){
	submitForm.distance.value = total_distance;
	submitForm.comments.value = "some comments";
	submitForm.points.value = convertToPolyline();

	return true;
}

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
var previousDistance = 0;
var mileDistance = 1.0;
var shouldUpdateAll;
var previousMarkerDistance =0;
var isRouteLineInit = false;
var mileMarkers = [];
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
function undoLastPoint(){
	var point = route_points.pop();
	route_line.deleteVertex(route_line.getVertexCount()- 1);		
	map.removeOverlay(point.marker);
	updateMileMarkers(true);
}
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
function outAndBack(){
	for(var i = route_points.length-1;i>=0;i--){
		addPoint(route_points[i].latlng);
	}	
}
