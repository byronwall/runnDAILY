<?php
$_SERVER["TIME_START"] = microtime(true);

DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");

require(SYSTEM_ROOT."/config.php");
session_start();

RoutingEngine::getInstance()->authenticateUser();
////User::$current_user->getPermissions();
//var_dump(User::$current_user);
//var_dump(RoutingEngine::getInstance()->requirePermission("GP__100", 1));
//die;
//
//$mems = Group::getMembers(1);
//var_dump($mems);
//die;
$request = explode("?", $_SERVER["REQUEST_URI"]);

$engine = RoutingEngine::getInstance()->initialize($request[0]);
$engine->renderRequest();

RoutingEngine::getInstance()->persistUserData();
?>