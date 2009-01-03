{{*
This is the template for the login page.
*}}
<h1>Login to the running site</h1>
<h3>you will be sent back to: {{$smarty.session.login_redirect}}</h3>
<form action="lib/action_login.php?action=login" method="post">

<input type="text" value="username" name="username">
<input type="password" value="password" name="password">
<input type="checkbox" name="remember" value=1">
<input type="submit" value="login">


</form>