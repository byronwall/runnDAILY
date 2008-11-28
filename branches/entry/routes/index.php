<?php
require("../lib/config.php");

/*
 * This is the index page for the routes folder.
 */

if($_SESSION["userData"]){
	$routes = Route::getRoutesForUser($_SESSION["userData"]->userID, 10);
}

$smarty->assign("route_list", $routes);
$content = $smarty->fetch("routes/index.tpl");

$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes - Running Site");
$smarty->display("master.tpl");
?>