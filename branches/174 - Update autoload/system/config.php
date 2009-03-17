<?php
DEFINE("COOKIE_NAME", "runndaily");

RoutingEngine::$controllers = array(
	"about_controller",
	"admin_controller",
	"community_controller",
	"error_controller",
	"feedback_controller",
	"help_controller",
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
function array_safe($arr, $key, $default = null){
	return (isset($arr[$key]))?$arr[$key]:$default;
}

function json_encode_null($a=false)
{
	if (is_null($a)) return 'null';
	if ($a === false) return 'false';
	if ($a === true) return 'true';
	if (is_scalar($a))
	{
		if (is_float($a))
		{
			// Always use "." for floats.
			return floatval(str_replace(",", ".", strval($a)));
		}

		if (is_string($a))
		{
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		else
		return $a;
	}
	$isList = true;
	for ($i = 0, reset($a); $i < count($a); $i++, next($a))
	{
		if (key($a) !== $i)
		{
			$isList = false;
			break;
		}
	}
	$result = array();
	if ($isList)
	{
		foreach ($a as $v){
			$result[] = json_encode_null($v);
		}
		return '[' . join(',', $result) . ']';
	}
	else
	{
		foreach ($a as $k => $v){
			if(is_null($v))continue;
			$result[] = json_encode_null($k).':'.json_encode_null($v);
		}
		return '{' . join(',', $result) . '}';
	}
}
?>