{{* This is the template for the account settings of a user. *}}
<div class="grid_12">
<h2 id="page-heading">Account Settings</h2>
</div>
<div class="clear"></div>

<div id="errors" class="grid_12"></div>
<div class="clear"></div>

<form name="settings_form" id="form_settings" action="/user/action_settings" method="post" onsubmit="checkSettings(this)">

<div class="grid_6">
	<h4>Account Information</h4>
	<p class="notice">Please select a username and enter a valid email address.</p>
	<input type="hidden" name="u_location_lat" value="" />
	<input type="hidden" name="u_location_lng" value="" />
	<p><label for="input_email">Email: </label><input id="input_email" type="text" name="u_email" value="{{$currentUser->email}}"></p>
	<p><label for="input_password">Password: </label><input id="input_password" type="password" name="u_password"></p>
	<p><label for="input_password2">Confirm Password: </label><input id="input_password2" type="password" name="u_password_confirm"></p>
</div>

<div class="grid_6">
	<h4>Personal Information</h4>
	<p class="notice">Personal information will be used to personalize your site experience.</p>
	<p><label for="input_realname">Real Name: </label><input type="text" id="input_realname" value="{{$currentUser->real_name}}" name="u_real_name"/></p>
	<p><label for="input_birthday">Birthday: </label><input type="text" id="input_birthday" name="u_birthday" value="{{$currentUser->birthday}}"/></p>
	<h4>Physical Information</h4>
	<p class="notice">Physical information will be used for calorie estimation and other quantitative purposes.</p>
	<p><label>Height (in): </label><input type="text" id="input_height" name="u_height" value="{{$currentUser->height}}"/></p>
	<p><label>Weight (lb): </label><input type="text" id="input_weight" name="u_weight" value="{{$currentUser->weight}}"/></p>
	<p><input type="submit" value="Update Settings"/></p>
</div>
<div class="clear"></div>

<div class="grid_12">
<h4>Home Location for Routes</h4>
	<p><label for="input_location">Location: </label><input type="text" id="input_location" name="location">
	<input type="button" onclick="show_address($('[name=location]').val())" value="Re-center" /></p>
	<p id="location_msg" class=""></p>
	<div id="map_placeholder" class="map"></div>
</div>

</form>
<div class="clear"></div>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(
	function(){
		Map.load("map_placeholder", register_click, {{if $currentUser->location_lat}}{{$currentUser->location_lat}}{{else}}null{{/if}}, {{if $currentUser->location_lng}}{{$currentUser->location_lng}}{{else}}null{{/if}});
		var validator = $("#form_register").validate({
			rules: {
			u_username: {
					required: true,
					minlength: 2,
					//remote: "/user/check_exists"
				},
				u_password: {
					required: true,
					minlength: 5
				},
				u_password_confirm: {
					required: true,
					minlength: 5,
					equalTo: "#input_password"
				},
				u_email: {
					required: true,
					email: true
				},
				u_birthday: {
					date: true
				},
				u_location_lat:{
					required: true
				}				
			},
			messages: {
				u_username: {
					required: "Enter a username.",
					minlength: jQuery.format("Enter at least {0} characters"),
					//remote: "Username is unavailable. Enter a new username."
				},
				u_password: {
					required: "Enter a password.",
					rangelength: jQuery.format("Password must be at least {0} characters")
				},
				u_password_confirm: {
					required: "Confirm your password.",
					minlength: jQuery.format("Enter at least {0} characters"),
					equalTo: "Passwords do not match."
				},
				u_email: {
					required: "Enter a valid email address.",
					minlength: "Enter a valid email address."
				},
				u_location_lat: {
					required: "Select your location on the map"
				}
			},
			submitHandler: function(form){
				$("input[type=text]").each( function(){
					if($(this).val() == "") $(this).attr("disabled", true);
				});
				form.submit();
			},
			errorLabelContainer: "#errors",
			wrapper: "p"
		});
	}
);
document.body.onunload = GUnload();

function register_click(overlay, latlng){
	if(latlng){
		Map.instance.clearOverlays();
		$("[name=u_location_lat]").val(latlng.lat());
		$("[name=u_location_lng]").val(latlng.lng());

		var icon_home = new GIcon();
		icon_home.image = "/img/icon_home.png";
		icon_home.shadow = "";
		icon_home.iconSize = new GSize(16, 16);
		icon_home.shadowSize = new GSize(0, 0);
		icon_home.iconAnchor = new GPoint(8, 8);
		icon_home.infoWindowAnchor = new GPoint(16, 16);
		var icon_home_options = {icon: icon_home, clickable: false};

		var markerOptions = { icon:icon_home, draggable:map_options.draggable };

		Map.instance.addOverlay(new GMarker(latlng, markerOptions));
	}
}

</script>