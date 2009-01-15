<?php
require("../lib/config.php");

if(!isset($_GET["format"])){
	header("location: http://{$_SERVER['SERVER_NAME']}/routes/");
	exit;
}

if($_GET["format"] == "ajax"){

	$uid = $_GET["uid"];
	$page_no = $_GET["page"];

	$routes = Route::getRoutesForUser($uid, 5, $page_no);

	$smarty->assign("routes", $routes);
	$smarty->assign("uid", $uid);
	$smarty->assign("page_no", $page_no+1);
	
	echo $smarty->fetch("routes/parts/route_list.tpl");
}
?>