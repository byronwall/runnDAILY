<?php
DEFINE("SITE_ROOT", dirname(dirname(__FILE__)));
DEFINE("CLASS_ROOT", SITE_ROOT."/lib/class");

function __autoload($class){
	$dirs = array(CLASS_ROOT."/", SITE_ROOT."/_smarty/", CLASS_ROOT."/rss/", CLASS_ROOT."/sql/", SYSTEM_ROOT."/controllers/");
	foreach($dirs as $dir){
		$path = $dir."class_".strtolower($class).".php";
		if(file_exists($path)){
			require_once($path);
			return;
		}
		$path = $dir.strtolower($class).".php";
		if(file_exists($path)){
			require_once($path);
			return;
		}
	}
}
function array_safe($arr, $key, $default = ""){
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
$page = Page::getPage(REQUEST);

User::$current_user->checkPermissions($page->min_permission);

Page::getSmarty()->assign("currentUser", User::$current_user);
Page::getSmarty()->assign("page", $page);
?>