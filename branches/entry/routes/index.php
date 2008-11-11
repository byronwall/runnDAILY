<?php
require("../lib/config.php");

/*
 * This is the index page for the routes folder.
 */

$content = $smarty->fetch("routes/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes .::. Running Site");
$smarty->display("master.tpl");
?>