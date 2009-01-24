<?php
require("../lib/config.php");

/*
 * This is the page for viewing an existing route.
 */

if(!isset($_GET["id"])){
	Page::redirect("/routes/");
}
$id = $_GET["id"];
$route = Route::fromRouteIdentifier($id);
$smarty->assign("route_view", $route);

$smarty->display_master("routes/view.tpl");
?>