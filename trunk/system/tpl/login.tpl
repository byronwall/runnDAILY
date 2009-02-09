{{*
This is the template for the login page.
*}}
<div class="grid_4 prefix_4 suffix_4">
<div class="box">
<div id="login-forms" class="block">
<form action="/user/login" method="post">
<fieldset class="login">

<p class="notice">Please enter your username and password.</p>

<p><label>Username: </label><input type="text" name="username"></p>
<p><label>Password: </label><input type="password" name="password"></p>
<p><label>Cookie? </label><input type="checkbox" name="remember" value="1"></p>
<input class="login" type="submit" value="Login">

</fieldset>
</form>
</div>
</div>
</div>