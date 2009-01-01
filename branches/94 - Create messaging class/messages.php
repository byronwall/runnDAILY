<?php
require("lib/config.php");

$msgs = Message::getMessagesToUser($user->userID);

$smarty->assign("messages_to", $msgs);
$content = $smarty->fetch("messages.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Community - Running Site");
$smarty->display("master.tpl");
?>