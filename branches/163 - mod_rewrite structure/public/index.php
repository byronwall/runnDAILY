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
$controller = new $class();

if(method_exists($controller, array_safe($type,2))){
	$controller->$type[2]();
}
else{
	$controller->index();
}
Page::getSmarty()->display_master($type[1]."/".array_safe($type,2).".tpl");

?>