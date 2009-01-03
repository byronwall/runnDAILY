<?php
require("config_class.php");

require_once($site_root."/_smarty/Smarty.class.php");

/*SET UP TEMPLATING ENGINE*/
$smarty = new Smarty();

$smarty->template_dir = $site_root."/tpl";
$smarty->compile_dir = $site_root."/_smarty/templates_c";
$smarty->cache_dir = $site_root."/_smarty/cache";
$smarty->config_dir = $site_root."/_smarty/configs";
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";

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
}
$user->checkPermissions();

/*GENERATE THE TEMPLATE THINGS FOR EVERY PAGE*/
$smarty->assign("currentUser", $user);
?>