<?php
require("config_class.php");

require_once(SITE_ROOT."/_smarty/Smarty.class.php");

/*SET UP TEMPLATING ENGINE*/
$smarty = new Smarty();

/*VALIDATE THE USER EVERYTIME*/
session_start();

if(!isset($_SESSION["userData"])){
	$user = User::cookieLogin();
	if($user){
		$_SESSION["userData"] = $user;
	}
	else{
		$user = new User();
	}
}
else{
	$user = $_SESSION["userData"];
	$user->refreshDetails();
}
$user->checkPermissions();

/*GENERATE THE TEMPLATE THINGS FOR EVERY PAGE*/
$smarty->assign("currentUser", $user);
?>