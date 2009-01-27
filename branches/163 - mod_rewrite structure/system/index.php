<?php
if(User::$current_user->uid){
	$recent_activity = Log::getAllActivityForUser(User::$current_user->uid);
	$smarty->assign("recent_activity_list", $recent_activity);
}

$smarty->assign("body_id", "body_home");
?>