<div class="grid_12">
<h2 id="page-heading">Account Settings</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<ul id="errors_box"></ul>
</div>
<div class="clear"></div>

<form name="settings_form" id="form_settings" action="/user/action_settings" method="post">

<div class="grid_6">
	<h4>Account Information</h4>
	<p class="notice">Please select a username and enter a valid email address.</p>
	<input type="hidden" name="u_location_lat" value="" />
	<input type="hidden" name="u_location_lng" value="" />
	<p><label for="input_email">Email: </label><input id="input_email" type="text" name="u_email" value="{{$currentUser->email}}"></p>
	<p><label for="input_email_confirm">Confirm Email: </label><input id="input_email_confirm" type="text" name="u_email_confirm"></p>
	<p><label for="input_password">Password: </label><input id="input_password" type="password" name="u_password"></p>
	<p><label for="input_password2">Confirm Password: </label><input id="input_password2" type="password" name="u_password_confirm"></p>
</div>

<div class="grid_6">
	<h4>Personal Information</h4>
	<p class="notice">Personal information will be used to personalize your site experience.</p>
	<p><label for="input_realname">Real Name: </label><input type="text" id="input_realname" value="{{$currentUser->settings.real_name}}" name="u_settings[real_name]"/></p>
	<p><label for="input_birthday">Birthday: </label><input type="text" id="input_birthday" name="u_settings[birthday]" value="{{$currentUser->settings.birthday}}"/></p>
	<h4>Physical Information</h4>
	<p class="notice">Physical information will be used for calorie estimation and other quantitative purposes.</p>
	<p><label>Height (in): </label><input type="text" id="input_height" name="u_settings[height]" value="{{$currentUser->settings.height}}"/></p>
	<p><label>Weight (lb): </label><input type="text" id="input_weight" name="u_settings[weight]" value="{{$currentUser->settings.weight}}"/></p>
	<p><input type="submit" value="Update Settings"/></p>
</div>
<div class="clear"></div>
</form>

<div class="grid_12">
<h4>Home Location for Routes</h4>
<form action="/" method="get" onsubmit="Geocoder.showAddress('#input_location');return false;">
	<p><label for="input_location">Location: </label><input type="text" id="input_location" name="location">
	<input type="submit" value="Re-center" /></p>
</form>
	<p id="location_msg" class=""></p>
	<div id="map_placeholder" class="map"></div>
</div>

<div class="clear"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxTwKQnnvD1T4H7IjqlIr-cK4JGBGBR9nTuCz-u_Of2k2UEZ7khhybXPyw" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(
	function(){
		Map.load("map_placeholder", register_click);

		{{if $currentUser->location_lat}}
			var LatLngCenter = new GLatLng({{$currentUser->location_lat}}, {{$currentUser->location_lng}});
			Map.instance.setCenter(LatLngCenter, 13);
			register_click(null, LatLngCenter);
		{{/if}}

		var validator = $("#form_settings").validate({
			onkeyup: true,
			onclick: true,
			onfocusout: false,
			rules: {
				u_password: {
					minlength: 5
				},
				u_password_confirm: {
					minlength: 5,
					equalTo: "#input_password"
				},
				u_email: {
					email: true
				},
				u_email_confirm: {
					email: true,
					equalTo: "#input_email"
				},
				u_birthday: {
					date: true
				},
				u_location_lat:{
					required: true
				}				
			},
			messages: {
				u_password: {
					rangelength: jQuery.format("Password must be at least {0} characters")
				},
				u_password_confirm: {
					minlength: jQuery.format("Enter at least {0} characters"),
					equalTo: "Passwords do not match."
				},
				u_email: {
					email: "Enter a valid email address."
				},
				u_email_confirm: {
					email: "Enter a valid email address.",
					equalTo: "Email addresses need to be the same."
				},
				u_location_lat: {
					required: "Select your location on the map"
				}
			},
			submitHandler: function(form){
				var change = false;
				$("input[name*=u_]").each(function(){
					if($(this).val() != $(this).data("init")){
						change = true;
					}
				});
				if(!change){
					$.facebox("Nothing has changed, so this will not submit.");
					return false;
				}
				
				$(":input").each( function(){
					if($(this).val() == "") $(this).attr("disabled", true);
				});
				form.submit();
			},
			errorLabelContainer: "#errors_box",
			wrapper: "li",
			errorClass: "error"
		});
			
		$("input[name*=u_]").each(function(){
			$(this).data("init", $(this).val());
		});
	}
);
document.body.onunload = GUnload;

function register_click(overlay, latlng){
	if(latlng){
		Map.instance.clearOverlays();
		$("[name=u_location_lat]").val(latlng.lat());
		$("[name=u_location_lng]").val(latlng.lng());

		var icon_home = new GIcon();
		icon_home.image = "/img/icon/home.png";
		icon_home.shadow = "";
		icon_home.iconSize = new GSize(16, 16);
		icon_home.shadowSize = new GSize(0, 0);
		icon_home.iconAnchor = new GPoint(8, 8);
		icon_home.infoWindowAnchor = new GPoint(16, 16);
		var icon_home_options = {icon: icon_home, clickable: false};
		var markerOptions = { icon:icon_home, draggable:Map.config.draggable };

		Map.instance.addOverlay(new GMarker(latlng, markerOptions));
	}
}

</script>