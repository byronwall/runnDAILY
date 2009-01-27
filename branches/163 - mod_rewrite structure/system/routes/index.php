<?php
$recent_routes = Route::getRoutesForUser(User::$current_user->uid, 6);
$recent_activity = Log::getRouteActivityForUser(User::$current_user->uid);
$more_routes = Route::getRoutesForUser(User::$current_user->uid, 50);

$smarty->assign("recent_route_list", $recent_routes);
$smarty->assign("recent_activity_list", $recent_activity);
$smarty->assign("all_route_list", $more_routes);
?>