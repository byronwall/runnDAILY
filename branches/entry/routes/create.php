<?php
require("../lib/config.php");

/*
 * This is the page for creating a new route.
 */

$content = $smarty->fetch("routes/create.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Routes .::. Running Site");
$smarty->display("master.tpl");
?>