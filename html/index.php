<?php
//phpinfo();
//die("Configuring the server.");
$_SERVER["TIME_START"] = microtime(true);

DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");
DEFINE("DB_USER", "db64581");
DEFINE("DB_PASS", "byron3chan");
DEFINE("DB_NAME", "db64581_running");
DEFINE("DB_HOST", $_ENV["DATABASE_SERVER"]);

require(SYSTEM_ROOT."/config.php");
session_start();

if(array_safe($_GET, "mediatemplebackdoor", false)){
	$_SESSION["DbBackdoor"] = true;
}

RoutingEngine::getInstance()->authenticateUser();

$request = explode("?", $_SERVER["REQUEST_URI"]);
$engine = RoutingEngine::getInstance()->initialize($request[0]);
$engine->renderRequest();
RoutingEngine::getInstance()->persistUserData();
?>