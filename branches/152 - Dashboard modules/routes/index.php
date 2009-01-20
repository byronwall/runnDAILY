<?php
require("../lib/config.php");

/*
 * This is the index page for the routes folder.
 */
if($_SESSION["userData"]){
	$recent_routes = Route::getRoutesForUser($_SESSION["userData"]->userID, 6);
	$recent_activity = Log::getRouteActivityForUser($_SESSION["userData"]->userID);
	$more_routes = Route::getRoutesForUser($_SESSION["userData"]->userID, 50);
}

if($_SESSION["userData"]->route_panels){
	$panels = explode(",", $_SESSION["userData"]->route_panels);
	foreach($panels as $panel){
		$page->addModule($panel);
	}
}
else{
	$page->addModule("routes_recent");
	$page->addModule("routes_recently_run");
	$page->addModule("routes_friends");
	$page->addModule("routes_parent_only");
}

$smarty->assign("recent_route_list", $recent_routes);
$smarty->assign("recent_activity_list", $recent_activity);
$smarty->assign("all_route_list", $more_routes);
$content = $smarty->fetch("routes/index.tpl");

$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes - Running Site");
$smarty->display("master.tpl");
?>