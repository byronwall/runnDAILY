<?php
$_SERVER["TIME_START"] = microtime(true);

DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");

require(SYSTEM_ROOT."/config.php");
session_start();

$request = explode("?", $_SERVER["REQUEST_URI"]);

$engine = RoutingEngine::getInstance()->initialize($request[0]);

if(isset($_SESSION["userData"]) && $_SESSION["userData"]->uid){
	User::$current_user = $_SESSION["userData"];
	User::$current_user->refreshDetails();
}
else{
	$_SESSION["userData"] = User::cookieLogin();
	User::$current_user = $_SESSION["userData"];
}

User::$current_user->checkPermissions($engine->page->min_permission);

RoutingEngine::getSmarty()->assign("currentUser", User::$current_user);
RoutingEngine::getSmarty()->assign("page", $engine->page);


$engine->renderRequest();
//TODO: implement some sort of selective rendering mechanism
/*
 * $smarty->display_rss()
 * $smarty->display_ajax()
 * $smarty->display_mini_master()
 */


?>