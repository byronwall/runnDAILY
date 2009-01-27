<?php
if(!isset($_GET["uid"])){
	header("Location: http://{$_SERVER['SERVER_NAME']}/community/");
	exit;
}

$uid = $_GET["uid"];

//get route data for the user.
$routes = Route::getRoutesForUser($uid, 5);
//get training data for the user.
$t_items = TrainingLog::getItemsForUserPaged($uid, 4);
//get log data for the user.
$l_items = Log::getAllActivityForUserPaged($uid, 5);

$smarty->assign("r_query", "u_uid={$uid}&page=1&count=5");
$smarty->assign("t_query", "u_uid={$uid}&page=1&count=5");
$smarty->assign("user_routes", $routes); 
$smarty->assign("user_training", $t_items); 
$smarty->assign("user_log", $l_items); 
$smarty->assign("user",User::fromUid($uid));
?>