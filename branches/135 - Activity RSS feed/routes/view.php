<?php
require("../lib/config.php");

/*
 * This is the page for viewing an existing route.
 */

if(!isset($_GET["id"])){
	header("location: http://". $_SERVER["SERVER_NAME"]."/routes/");
	exit;
}
$id = $_GET["id"];
$route = Route::fromRouteIdentifier($id);
$smarty->assign("route_view", $route);

$content = $smarty->fetch("routes/view.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes - Running Site");
$smarty->display("master.tpl");
?>