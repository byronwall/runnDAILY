<?php
require("lib/config.php");

/*
 * This is the page for logging into the site.
 */

$content = $smarty->fetch("login.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Login .::. Running Site");
$smarty->display("master.tpl");
?>