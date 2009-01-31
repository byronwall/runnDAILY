<?php
DEFINE("COOKIE_NAME", "runndaily");

RoutingEngine::$controllers = array(
	"about_controller",
	"admin_controller",
	"community_controller",
	"error_controller",
	"feedback_controller",
	"home_controller",
	"log_controller",
	"message_controller",
	"routes_controller",
	"rss_controller",
	"training_controller",
	"user_controller"
);

function __autoload($class){
	$dirs = array(CLASS_ROOT."/", SYSTEM_ROOT."/_smarty/", CLASS_ROOT."/rss/", CLASS_ROOT."/sql/", SYSTEM_ROOT."/controllers/");
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

?>