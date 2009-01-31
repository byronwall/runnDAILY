<?php
DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");

require(SYSTEM_ROOT."/config.php");
session_start();

$request = explode("?", $_SERVER["REQUEST_URI"]);
$type = explode("/", $request[0]);

if(isset($_SESSION["userData"]) && $_SESSION["userData"]->uid){
	User::$current_user = $_SESSION["userData"];
	User::$current_user->refreshDetails();
}
else{
	$_SESSION["userData"] = User::cookieLogin();
	User::$current_user = $_SESSION["userData"];
}
$page = Page::getPage($request[0]);

User::$current_user->checkPermissions($page->min_permission);

Page::getSmarty()->assign("currentUser", User::$current_user);
Page::getSmarty()->assign("page", $page);

if(!Page::getControllerExists($type[1])){
	$class = "home_controller";
	$type[2]=$type[1];
}
else{
	$class = strtolower($type[1])."_controller";
}
//TODO:move this into the page class
$controller = new $class();

if(method_exists($controller, array_safe($type,2))){
	$controller->$type[2]();
}
else{
	$controller->index();
	$page->page_name .= "/index";
}
//TODO: implement some sort of selective rendering mechanism
/*
 * $smarty->display_rss()
 * $smarty->display_ajax()
 * $smarty->display_mini_master()
 */
Page::getSmarty()->display_master($page->getTemplateName());

?>