{{*
This is the template for the index page of the root folder.
*}}
<h1>Home page</h1>
<h2>Welcome to the Running Site</h2>

{{if $currentUser->isAuthenticated}}
This will soon become your customized dashboard.
{{if $currentUser->type eq 301}}
<a href="/lib/action_login.php?action=activate&uid={{$currentUser->userID}}&hash={{$currentUser->cookie_hash}}">activate</a>
{{/if}}
{{else}}
There are several things you can do as a new user!

<div><a href="/login.php">Login</a></div>
<div><a href="/register.php">Register</a></div>
<div><a href="/routes/create.php">Create a route</a></div>

{{/if}}