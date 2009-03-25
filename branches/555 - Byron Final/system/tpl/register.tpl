{{* This is the template for the registration of a new user. *}}
<div class="grid_12">
<h2 id="page-heading">Register a New Account</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<p class="icon"><img src="/img/icon_exclamation_small.png"/>Required field.</p>
</div>
<div class="clear"></div>

<div class="grid_12">
	<ul id="errors_box"></ul>
</div>
<div class="clear"></div>



<form enctype="multipart/form-data" action="/user/register" method="post" id="form_register">
<div class="grid_6">
	<h4>Account Information</h4>
	<p class="notice">Please select a username and enter a valid email address.</p>
	<input type="hidden" name="u_location_lat" value="" />
	<input type="hidden" name="u_location_lng" value="" />
	<p><label for="input_email">Email: </label><input id="input_email" type="text" name="u_email"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_username">Username: </label><input id="input_username" type="text" name="u_username"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_password">Password: </label><input id="input_password" type="password" name="u_password"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_password2">Confirm Password: </label><input id="input_password2" type="password" name="u_password_confirm"> <img src="/img/icon_exclamation_small.png"/></p>
</div>

<div class="grid_6">
	<h4>Personal Information</h4>
	<p class="notice">Personal information will be used to personalize your site experience.</p>
	<p><label for="input_realname">Real Name: </label><input type="text" id="input_realname" name="u_settings[real_name]"/></p>
	<p><label for="input_birthday">Birthday: </label><input type="text" id="input_birthday" name="u_settings[birthday]"/></p>
	<p>
		<label>User Image: </label>
		<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
		<input type="file" name="user_img">
	</p>
	<h4>Physical Information</h4>
	<p class="notice">Physical information will be used for calorie estimation and other quantitative purposes.</p>
	<p><label>Height (in): </label><input type="text" id="input_height" name="u_settings[height]"/></p>
	<p><label>Weight (lb): </label><input type="text" id="input_weight" name="u_settings[weight]"/></p>
	<p><input type="submit" value="Register"/></p>
</div>
<div class="clear"></div>

<div class="grid_12">

<h4>Home Location for Routes</h4>
	<p><label for="input_location">Location: </label><input type="text" id="input_location" name="location">
	<input type="button" onclick="Geocoder.showAddress('#input_location');return false;" value="Re-center" /></p>
	<p id="location_msg" class=""></p>
	<div id="map_placeholder" class="map"></div>
</div>
</form>
<div class="clear"></div>

{{include file="routes/parts/script.tpl"}}
<script type="text/javascript">

$(document).ready(
	function(){
		Map.load("map_placeholder", register_click);
		var validator = $("#form_register").validate({
			onkeyup: false,
			onclick: true,
			onfocusout: false,
			rules: {
				u_username: {
					required: true,
					minlength: 2,
					remote: "/user/check_exists"
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
					remote: "Username is unavailable. Enter a new username."
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
			errorLabelContainer: "#errors_box",
			wrapper: "li",
			errorClass: "error"
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

		var markerOptions = { icon:icon_home, draggable:Map.config.draggable};

		Map.instance.addOverlay(new GMarker(latlng, markerOptions));
	}
}

</script>