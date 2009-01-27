<?php
if(!isset($_GET["id"])){
	Page::redirect("/routes/");
}
$id = $_GET["id"];
$route = Route::fromRouteIdentifier($id);
$smarty->assign("route_view", $route);
?>