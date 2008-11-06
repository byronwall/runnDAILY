<?php
require_once("php/settings.php");

$content=$smarty->fetch("settings.tpl");

$smarty->assign("content", $content);

$smarty->assign("title", "users on the site");

$smarty->display("master.tpl");

?>