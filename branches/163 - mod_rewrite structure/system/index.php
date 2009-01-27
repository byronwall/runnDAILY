<?php
require("lib/config.php");

/*
 * This is the main index for the site.
 * It will either load the dashboard or a generic home page.
 */
if(User::$current_user->uid){
	$recent_activity = Log::getAllActivityForUser(User::$current_user->uid);
	$smarty->assign("recent_activity_list", $recent_activity);
}

$smarty->assign("body_id", "body_home");
$smarty->display_master("index.tpl");
?>