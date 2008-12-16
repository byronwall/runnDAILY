<?php
require("../lib/config.php");

/*
 * This is the index page for the routes folder.
 */

if($_SESSION["userData"]){
	$routes = Route::getRoutesForUser($_SESSION["userData"]->userID, 10);
	$recent_activity = Log::getRouteActivityForUser($_SESSION["userData"]->userID);
}

$smarty->assign("route_list", $routes);
$smarty->assign("recent_list", $recent_activity);
$content = $smarty->fetch("routes/index.tpl");

$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes - Running Site");
$smarty->display("master.tpl");
?>