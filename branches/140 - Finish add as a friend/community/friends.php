<?php
require("../lib/config.php");

$smarty->assign("users_friends", $user->getFriends());

$content = $smarty->fetch("community/friends.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Community - Running Site");
$smarty->display("master.tpl");
?>