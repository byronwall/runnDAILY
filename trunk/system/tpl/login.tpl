{{*
This is the template for the login page.
*}}
<div class="grid_12">
	<h2 id="page-heading">Login</h2>
</div>
<div class="clear"></div>
<div class="grid_12">
	<div>
		<div>
			<form action="/user/login" method="post">
					<p class="notice">Please enter your username and password.</p>
									
					<p><label>Username: </label><input type="text" name="username"></p>
					<p><label>Password: </label><input type="password" name="password"></p>
					<p><label>Stay Logged In? </label><input type="checkbox" name="remember" value="1"></p>
					
					<input class="login" type="submit" value="Login">
			</form>
		</div>
	</div>
</div>

<div class="clear"></div>