<?php
DEFINE("COOKIE_NAME", "runndaily");

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