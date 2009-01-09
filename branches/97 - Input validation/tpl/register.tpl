{{* This is the template for the registration of a new user. *}}
<h1>Register a new account on the running site</h1>
<form action="/lib/action_login.php?action=register" method="post" id="form_register">
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
		var validator = $("#form_register").validate({
			rules: {
				username: {
					required: true,
					minlength: 2,
					remote: "/lib/ajax_username.php"
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
					required: "Repeat your password",
					minlength: jQuery.format("Enter at least {0} characters"),
					equalTo: "Enter the same password as above"
				},
				email: {
					required: "Please enter a valid email address",
					minlength: "Please enter a valid email address"
				},
				birthday: {
					date: "Enter mm/dd/yyyy"
				}
			},
			// the errorPlacement has to take the table layout into account
			errorPlacement: function(error, element) {
				if ( element.is(":checkbox") )
					error.appendTo ( element.next() );
				else if( element.is(":hidden") )
					alert(error.text());				
				else
					error.appendTo( element.parent() );
			},
			// specifying a submitHandler prevents the default submit, good for the demo
			submitHandler: function() {
				alert("submitted!");
			},
			// set this class to error-labels to indicate valid fields
			success: function(label) {
				// set &nbsp; as text for IE
				label.html("&nbsp;").addClass("checked");
			}
		});
			
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

</script>