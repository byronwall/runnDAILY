<?php
require("../lib/config.php");

//get user routes
$stmt = Database::getDB()->prepare("
	SELECT r_name, r_distance, r_id
	FROM routes
	WHERE
		r_uid = ?
");
$stmt->bind_param("i", User::$current_user->uid);
$stmt->execute();
$stmt->store_result();

$routes = array();
while($row = $stmt->fetch_assoc()){
	$route = new Route($row);
	$routes[$route->id] = $route;
}
$stmt->close();
//get training types

$stmt = Database::getDB()->prepare("
	SELECT t_type_id, t_type_name
	FROM training_types
");
$stmt->execute();
$stmt->store_result();
$types = array();
while($row = $stmt->fetch_assoc()){
	$types[] = array("id"=>$row["t_type_id"], "name"=>$row["t_type_name"]);
}
$stmt->close();

$smarty->assign("t_types", $types);
$smarty->assign("routes_json", json_encode_null($routes));
$smarty->assign("routes", $routes);
$smarty->display_master("training/create.tpl");

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