<?php
require("../lib/config.php");

/*
 * This is the index page for the community folder.
 */

$content = $smarty->fetch("community/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Community .::. Running Site");
$smarty->display("master.tpl");
?>