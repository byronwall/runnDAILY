{{*
This is the template for the index page of the root folder.
*}}
<h1>Home page</h1>
<h2>Welcome to the Running Site</h2>

{{if $currentUser->isAuthenticated}}
This will soon become your customized dashboard.
{{else}}
There are several things you can do as a new user!

<div><a href="/login.php">Login</a></div>
<div><a href="/register.php">Register</a></div>
<div><a href="/routes/create.php">Create a route</a></div>

{{/if}}

<script type="text/javascript">

$(document).ready(
	function(){
		$("#tab_home").removeClass("tab_inactive");
		$("#tab_home").addClass("tab_active");
	}
);

</script>