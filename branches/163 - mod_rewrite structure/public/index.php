<?php
DEFINE("PUBLIC_ROOT", dirname(__FILE__));
DEFINE("ROOT", dirname(PUBLIC_ROOT));
DEFINE("SYSTEM_ROOT", ROOT . "/system");


$type = explode("/", $_SERVER["REQUEST_URI"]);
$type = $type[0];

$request = explode("?", $_SERVER["REQUEST_URI"]);
DEFINE("REQUEST", $request[0] );

require(SYSTEM_ROOT."/lib/config.php");

$path = SYSTEM_ROOT.REQUEST;
//var_dump($GLOBALS);
if(file_exists($path)){
	include($path);
}

$smarty->display_master($page->getTemplateName());

?>