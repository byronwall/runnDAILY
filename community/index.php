<?php
require("../lib/config.php");

/*
 * This is the index page for the community folder.
 */

$smarty->assign("users_all", User::getListOfUsers());
$smarty->assign("users_friends", User::$current_user->getFriends());

$smarty->display_master("community/index.tpl");
?>