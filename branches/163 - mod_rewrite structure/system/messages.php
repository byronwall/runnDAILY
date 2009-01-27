<?php
$msgs_to = Message::getMessagesForUser(User::$current_user->uid, true);
$msgs_from = Message::getMessagesForUser(User::$current_user->uid, false);

$smarty->assign("messages_to", $msgs_to);
$smarty->assign("messages_from", $msgs_from);
?>