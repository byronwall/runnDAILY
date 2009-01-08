{{* This is the template for the registration of a new user. *}}
<h1>Register a new account on the running site</h1>
<form action="/lib/action_login.php?action=register" method="post" onsubmit="return checkPasswords();">
<div id="reg_ctain">
<div id="reg_in_ctain">
<h2>user details</h2>
<ul>
	<li><label for="input_email">email</label><input id="input_email" type="text" name="email"></li>
	<li><label for="input_username">username</label><input id="input_username" type="text" name="username"></li>
	<li><label for="input_password">password</label><input id="input_password" type="password" name="password"></li>
	<li><label for="input_password2">password</label><input id="input_password2" type="password" name="password_confirm"></li>
	<li><a id="a_checkname" href="#" onclick="return false;">check availability</a></li>
</ul>
<h2>personal information</h2>
<ul>
	<li><label for="input_realname">real name</label><input type="text" id="input_realname" name="real_name"></li>
	<li><label for="input_birthday">birthday</label><input type="text" id="input_birthday" name="birthday"></li>
</ul>
<div id="reg_loc_sub">
	<input type="submit" value="register">
</div>
</div>
<div id="reg_loc_ctain">
<h2>geographic details</h2>
	<label for="input_location">home location</label><input type="text" id="input_location" name="location">
	<div id="map_placeholder" class="small_map"></div>
</div>
</div>
</form>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAYZcibhuwr8GMgCWYwqU-RxQzNv4mzrEKtvvUg4SKGFnPU6pUNBTkQL_qSiLmJQ3qE-zNxRFJgRZM8g" type="text/javascript"></script>
<script src="/js/map.js" type="text/javascript"></script>
<script type="text/javascript">

$(document).ready(
	function(){
		load("map_placeholder", register_click);
	}
);
document.body.onunload = GUnload();

function register_click(overlay, latlng){
	var geocoder = new GClientGeocoder();
	geocoder.getLocations(
		latlng,
		function(response){
			alert(response.Status.code);
			$.each(
				response.Placemark,
				function(index, item){
					if(item.AddressDetails.Accuracy == 4 || item.AddressDetails.Accuracy == 5){
						alert(item.address + "    " + item.AddressDetails.Accuracy);
					}
				}
			);
			
		}
	);
}

function checkPasswords(){
	return $("#input_password").val() == $("#input_password2").val();
}

$("#a_checkname").click(
	function(){
		$.get(
			"/lib/ajax_username.php",
			{username:$("#input_username").val()},
			function(data){
				alert(data);
			},
			"text"
		);
		return false;
	}
);

</script>