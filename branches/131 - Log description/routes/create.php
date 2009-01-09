<?php
require("../lib/config.php");

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

$content = $smarty->fetch("routes/create.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes - Running Site");
$smarty->assign("body_id", "map_create");
$smarty->display("master.tpl");
?>