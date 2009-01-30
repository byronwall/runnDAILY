<?php
DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");


$type = explode("/", $_SERVER["REQUEST_URI"]);

$request = explode("?", $_SERVER["REQUEST_URI"]);
DEFINE("REQUEST", $request[0] );

require(SYSTEM_ROOT."/lib/config.php");

if(!Page::getControllerExists($type[1])){
	$class = "home_controller";
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
}
//TODO: implement some sort of selective rendering mechanism
/*
 * $smarty->display_rss()
 * $smarty->display_ajax()
 * $smarty->display_mini_master()
 */
Page::getSmarty()->display_master($page->getTemplateName());

?>