<?php
require("lib/config.php");

$msgs_to = Message::getMessagesForUser($user->userID, true);
$msgs_from = Message::getMessagesForUser($user->userID, false);

$smarty->assign("messages_to", $msgs_to);
$smarty->assign("messages_from", $msgs_from);
$content = $smarty->fetch("messages.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Community - Running Site");
$smarty->display("master.tpl");
?>