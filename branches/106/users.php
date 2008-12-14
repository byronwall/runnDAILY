<?php
require_once("php/settings.php");

$action = isset($_GET["action"])?$_GET["action"]:NULL;

switch($action){
	case "profile":
		$username = $_GET["username"];

		$user_profile = User::fromUsername($username);
		
		$smarty->assign("user", $user_profile);
		
		//get the routes by the user
		$routes = Route::getRoutesForUser($user_profile);
		$smarty->assign("user_routes", $routes);
		
		$content = $smarty->fetch("users/profile.tpl");

		break;

	default:
		$smarty->assign("users", User::getListOfUsers());

		$content = $smarty->fetch("users/userList.tpl");
		break;

}


$smarty->assign("content", $content);

$smarty->assign("title", "users on the site");

$smarty->display("master.tpl");

?>