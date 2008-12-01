<?php
require("lib/config.php");

/*
 * This is the page for registering a new user.
 */

$content = $smarty->fetch("register.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Register .::. Running Site");
$smarty->display("master.tpl");
?>