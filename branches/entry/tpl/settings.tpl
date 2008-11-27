<h1>settings for {{$currentUser->username}}</h1>

<form name="settings_form" id="form_settings" action="/lib/action_settings.php" method="post" onsubmit="checkSettings(this)">

<h2>home location</h2>

<div id="map_home_location"></div>

<input type="hidden" id="input_user_home_lat" name="user_home_lat" value="test_lat">
<input type="hidden" id="input_user_home_lng" name="user_home_lng" value="test_lng">

<h2>general preferences</h2>
<h2>map specific settings</h2>

<input type="submit" value="update settings">

</form>

<style type="text/css">

#map_home_location{
height:300px;
width:300px;
}

</style>

<script type="text/javascript">



$(document).ready( function(){
	load("map_home_location", map_settings_click);
	
	{{if !$currentUser->location_lat|@is_null}}
		user_options.latlng_start = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
		map.setCenter(user_options.latlng_start);
		var home_marker = new GMarker(user_options.latlng_start);
		map.addOverlay(home_marker);
	{{/if}}
});

document.body.onunload = GUnload();

var home_marker = new GMarker(user_options.latlng_start);

function loadMiniMap(){
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map_home_location"), {mapTypes:[G_NORMAL_MAP,G_SATELLITE_MAP,G_HYBRID_MAP]});
		map.setCenter(new GLatLng(40.4242126,-86.930522), 13);
		GEvent.addListener(map,"click", map_click);
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		map.enableScrollWheelZoom();
		
		map.addOverlay(home_marker);
	}
}
function map_settings_click(overlay, latlng){
	if(latlng){
		map.clearOverlays();
		home_marker = new GMarker(latlng);
		map.addOverlay(home_marker);
		
		document.settings_form.user_home_lat.value = latlng.lat();
		document.settings_form.user_home_lng.value = latlng.lng();
	}
}
function checkSettings(form_DOM){
	
}

</script>