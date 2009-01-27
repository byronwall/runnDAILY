<?php
$smarty->assign("users_all", User::getListOfUsers());
$smarty->assign("users_friends", User::$current_user->getFriends());
?>