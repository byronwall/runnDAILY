{{* This is the template for the registration of a new user. *}}
<div class="grid_12">
<h2 id="page-heading">New Account Registration</h2>
</div>
<div class="clear"></div>

<form enctype="multipart/form-data" action="/user/register" method="post" id="form_register">
<div class="grid_4">
	<h2>Account Information</h2>
	<p class="notice">Please select a username, enter a valid email address, and choose a password for your new account.</p>
	<input type="hidden" name="u_location_lat" value="" />
	<input type="hidden" name="u_location_lng" value="" />
	<p><label for="input_username">Username: </label><input id="input_username" type="text" name="u_username"></p>
	<p><label for="input_email">Email: </label><input id="input_email" type="text" name="u_email"></p>
	<p><label for="input_email_confirm">Confirm Email: </label><input id="input_email_confirm" type="text" name="u_email_confirm"></p>
	<p><label for="input_password">Password: </label><input id="input_password" type="password" name="u_password"></p>
	<p><label for="input_password2">Confirm Password: </label><input id="input_password2" type="password" name="u_password_confirm"></p>
	<p><input type="submit" value="Register" /></p>
	<div id="error_box"></div>
</div>

<div class="grid_8">
<h2>Home Location</h2>
	<p class="alert_red">You must select a home location on the map below.</p>
	<p class="notice">If you would like to re-center the map, you may search by address, city, state, or ZIP code.</p>
	<p><label for="input_location">Search Location: </label><input type="text" id="input_location" name="location">
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
				u_email_confirm: {
					required: true,
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
					equalTo: "Password and confirmation password need to match do not match."
				},
				u_email: {
					required: "Enter a valid email address.",
					email: "Enter a valid email address."
				},
				u_email_confirm: {
					required: "Confirm your valid email address.",
					email: "Confirm your valid email address.",
					equalTo: "Email address and confirmation address do not match."
				},
				u_location_lat: {
					required: "Select your home location on the map to the right."
				}
			},
			submitHandler: function(form){
				$("input[type=text]").each( function(){
					if($(this).val() == "") $(this).attr("disabled", true);
				});
				form.submit();
			},
			errorLabelContainer: "#error_box",
			errorElement: "p"
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