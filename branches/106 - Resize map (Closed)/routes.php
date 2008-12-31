<?php
require("php/settings.php");

$action = (isset($_GET["action"]))?$_GET["action"]:NULL;
switch($action){
	case "view":
		$route_id = $_GET["id"];

		$route = Route::fromRouteIdentifier($route_id);

		$smarty->assign("route", $route);
		$content = $smarty->fetch("routes/viewRoute.tpl");

		break;
	default:
		$smarty->assign("routes", Route::getAllRoutes());
		$content = $smarty->fetch("routes/routeList.tpl");

		break;
}

$smarty->assign("content", $content);
$smarty->assign("title", "routes");
$smarty->display("master.tpl");
?>