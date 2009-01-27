<?php

/*
 * This is the page for creating a new route.
 */

if(isset($_GET["rid"])){
	$route = Route::fromRouteIdentifier($_GET["rid"]);
	$smarty->assign("route_edit", $route);
	$smarty->assign("is_edit", true);
	
	if(isset($_GET["mode"])){
		$isCopy = $_GET["mode"] == "copy";
		$smarty->assign("isCopy", $isCopy);
	}
}

$smarty->assign("body_id", "map_create");
?>