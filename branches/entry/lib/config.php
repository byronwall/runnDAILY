<?php
$SETTINGS = array();

//This is simply to make sure that links work regardless of which server is giving access.
if(file_exists("settings/settings.ini")){
	$SETTINGS = parse_ini_file("settings/settings.ini");
}
else{
	$SETTINGS["address"] = "http://byroni.us/maps";
}

$DEBUG = array();



require_once("class/ext_mysqli.php");

$SETTINGS["dbconn"] = $mysqli = new mysqli_Extended("localhost", "byron", "abcd1234", "running");

require_once("class/class_user.php");
require_once("class/class_route.php");

//Smarty exists in the include-path in php.ini of the server
require_once("./_smarty/Smarty.class.php");


/*SET UP TEMPLATING ENGINE*/
$smarty = new Smarty();

$smarty->template_dir = "./tpl";
$smarty->compile_dir = "./_smarty/templates_c";
$smarty->cache_dir = "./_smarty/cache";
$smarty->config_dir = "./_smarty/configs";
$smarty->left_delimiter = "{{";
$smarty->right_delimiter = "}}";

/*VALIDATE THE USER EVERYTIME*/
$user = new User();
$user->validateUser();

if(isset($_SESSION["userData"])){
	$user = $_SESSION["userData"];
}

/*GENERATE THE TEMPLATE THINGS FOR EVERY PAGE*/
$smarty->assign("activeUser", $user->validUser);
$smarty->assign("currentUser", $user);
$smarty->assign("activeUserName", $user->username);

?>