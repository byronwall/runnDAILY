{{*
This is the template for the login page.
*}}

<form action="lib/action_login.php?action=login" method="post">

<input type="text" value="byron" name="username">
<input type="text" value="password" name="password">
<input type="checkbox" name="remember" value=1">
<input type="submit" value="login">
<input type="hidden" name="refer" value="{{$smarty.get.refer}}">


</form>