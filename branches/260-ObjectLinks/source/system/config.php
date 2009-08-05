<?php
DEFINE("COOKIE_NAME", "runndaily");

DEFINE("DB_USER", "db64581");
DEFINE("DB_PASS", "byron3chan");
DEFINE("DB_NAME", "db64581_running");
DEFINE("DB_HOST", $_ENV["DATABASE_SERVER"]);

spl_autoload_register("site_autoload");

function site_autoload($class){
	$class = strtolower($class);
//	if($class == "smarty") return require(CLASS_ROOT."/smarty/Smarty.php");
	
	$path = CLASS_ROOT. "/" . str_replace("_", "/", $class) . ".php";
	//if(file_exists($path)){
		require($path);
	//}
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