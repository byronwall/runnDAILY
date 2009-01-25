<?php
DEFINE("SITE_ROOT", dirname(dirname(__FILE__)));
DEFINE("CLASS_ROOT", SITE_ROOT."/lib/class");

function __autoload($class){
	$dirs = array(CLASS_ROOT."/", SITE_ROOT."/_smarty/", CLASS_ROOT."/rss/", CLASS_ROOT."/sql/");
	foreach($dirs as $dir){
		$path = $dir."class_".strtolower($class).".php";
		if(file_exists($path)){
			require_once($path);
			return;
		}
	}
}
function array_safe($arr, $key, $default = null){
	return (isset($arr[$key]))?$arr[$key]:$default;
}
session_start();

if(isset($_SESSION["userData"]) && $_SESSION["userData"]->uid){
	User::$current_user = $_SESSION["userData"];
	User::$current_user->refreshDetails();
}
else{
	$_SESSION["userData"] = User::cookieLogin();
	User::$current_user = $_SESSION["userData"];
}
$page = Page::getPage($_SERVER["SCRIPT_NAME"]);

User::$current_user->checkPermissions($page->min_permission);

$smarty = new Smarty_Ext();
$smarty->assign("currentUser", User::$current_user);
$smarty->assign("page", $page);
?>