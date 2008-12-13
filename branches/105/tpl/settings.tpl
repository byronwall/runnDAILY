<h1>settings for {{$currentUser->username}}</h1>

<form name="settings_form" id="form_settings" action="/lib/action_settings.php" method="post" onsubmit="checkSettings(this)">

<h2>user details</h2>
<ul>
	<li><label for="input_email">email</label><input id="input_email" type="text" name="u_email" value="{{$currentUser->u_email}}"></li>
	<li><label for="input_password">password</label><input id="input_password" type="password" name="password"></li>
	<li><label for="input_password2">confirm password</label><input id="input_password2" type="password" name="password_confirm"></li>
</ul>
<h2>personal information</h2>
<ul>
	<li><label for="input_realname">real name</label><input type="text" id="input_realname" name="real_name"></li>
	<li><label for="input_birthday">birthday</label><input type="text" id="input_birthday" name="birthday"></li>
</ul>
<h2>geographic details</h2>
<ul>
	<li>
		<div id="map_home_location"></div>
	</li>
</ul>

<input type="hidden" id="input_user_home_lat" name="user_home_lat" value="{{$currentUser->location_lat}}">
<input type="hidden" id="input_user_home_lng" name="user_home_lng" value="{{$currentUser->location_lng}}">

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