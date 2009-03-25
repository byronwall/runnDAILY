<?php
//phpinfo();
//die("Configuring the server.");
$_SERVER["TIME_START"] = microtime(true);

DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");

require(SYSTEM_ROOT."/config.php");
session_start();

if(array_safe($_GET, "mediatemplebackdoor", false)){
	$_SESSION["DbBackdoor"] = true;
}

/*
if(array_safe($_SESSION, "DbBackdoor", false)){
	DEFINE("DB_USER", "db64581");
	DEFINE("DB_PASS", "byron3chan");
	DEFINE("DB_NAME", "db64581_running");
	DEFINE("DB_HOST", $_ENV["DATABASE_SERVER"]);
}*/
//else{
	DEFINE("DB_USER", "thechanmane");
	DEFINE("DB_PASS", "ic'an'cu88");
	DEFINE("DB_NAME", "running");
	DEFINE("DB_HOST", "98.223.231.125");
	
//}

RoutingEngine::getInstance()->authenticateUser();

$request = explode("?", $_SERVER["REQUEST_URI"]);
$engine = RoutingEngine::getInstance()->initialize($request[0]);
$engine->renderRequest();
RoutingEngine::getInstance()->persistUserData();
?>