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



require_once("classes/mysqli_extensions.php");

$SETTINGS["dbconn"] = $mysqli = new mysqli_Extended("localhost", "byron", "abcd1234", "running");

require_once("classes/userClass.php");
require_once("classes/routeClass.php");
require_once("Smarty.class.php");


/*SET UP TEMPLATING ENGINE*/
$smarty = new Smarty();

$smarty->template_dir = '.\smarty\templates';
$smarty->compile_dir = '.\smarty\templates_c';
$smarty->cache_dir = '.\smarty\cache';
$smarty->config_dir = '.\smarty\configs';
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