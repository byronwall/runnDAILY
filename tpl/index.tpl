{{*
This is the template for the index page of the root folder.
*}}
<h1>run'n Daily</h1>

{{if $currentUser->isAuthenticated}}
{{$currentUser->username}}, welcome to your personalized dashboard!
{{else}}
There are several things you can do as a new user!

<a href="/login.php">Login</a>
<a href="/register.php">Register</a>
<a href="/routes/create.php">Create a route</a>

{{/if}}