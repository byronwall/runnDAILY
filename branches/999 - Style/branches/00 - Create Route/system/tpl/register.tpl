{{* This is the template for the registration of a new user. *}}
<div class="grid_12">
<h2 id="page-heading">Register a New Account</h2>
</div>
<div class="clear"></div>

<div class="grid_12">
	<p class="icon"><img src="/img/icon_exclamation_small.png"/>Required field.</p>
</div>
<div class="clear"></div>

<form action="/user/register" method="post" id="form_register">
<div class="grid_6">
	<h4>Account Information</h4>
	<input type="hidden" name="u_location_lat" value="" />
	<input type="hidden" name="u_location_lng" value="" />
	<p><label for="input_email">Email: </label><input id="input_email" type="text" name="u_email"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_username">Username: </label><input id="input_username" type="text" name="u_username"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_password">Password: </label><input id="input_password" type="password" name="u_password"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><label for="input_password2">Confirm Password: </label><input id="input_password2" type="password" name="u_password_confirm"> <img src="/img/icon_exclamation_small.png"/></p>
	<p><a id="a_checkname" href="#" onclick="return false;">Check username availability.</a></p>
</div>

<div class="grid_6">
	<h4>Personal Information</h4>
	<p><label for="input_realname">Real Name: </label><input type="text" id="input_realname" name="u_real_name"/></p>
	<p><label for="input_birthday">Birthday: </label><input type="text" id="input_birthday" name="u_birthday"/></p>
	<p><input type="submit" value="Register"/></p>
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
		load("map_placeholder", register_click);
		var validator = $("#form_register").validate({
			rules: {
				username: {
					required: true,
					minlength: 2,
					remote: "/user/check_exists"
				},
				password: {
					required: true,
					minlength: 5
				},
				password_confirm: {
					required: true,
					minlength: 5,
					equalTo: "#input_password"
				},
				email: {
					required: true,
					email: true
				},
				birthday: {
					date: true
				},
				u_location_lat:{
					required: true
				}				
			},
			messages: {
				username: {
					required: "Enter a username",
					minlength: jQuery.format("Enter at least {0} characters"),
					remote: "Username taken."
				},
				password: {
					required: "Provide a password",
					rangelength: jQuery.format("Enter at least {0} characters")
				},
				password_confirm: {
					required: "Repeat the password to confirm",
					minlength: jQuery.format("Enter at least {0} characters"),
					equalTo: "Enter the same password as above"
				},
				email: {
					required: "Please enter a valid email address",
					minlength: "Please enter a valid email address"
				},
				birthday: {
					date: "Enter mm/dd/yyyy"
				},
				u_start_lat: {
					required: "Select your location on the map"
				}
			},
			submitHandler: function(form){
				$("input[type=text]").each( function(){
					if($(this).val() == "") $(this).attr("disabled", true);
				});
				form.submit();
			},
			errorLabelContainer: "#errors ul",
			wrapper: "li"
		});
	}
);
document.body.onunload = GUnload();

function register_click(overlay, latlng){
	if(latlng){
		map.clearOverlays();
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

		map.addOverlay(new GMarker(latlng, markerOptions));
	}
}

</script>