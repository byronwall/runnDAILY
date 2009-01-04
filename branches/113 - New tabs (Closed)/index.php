<?php
require("lib/config.php");

/*
 * This is the main index for the site.
 * It will either load the dashboard or a generic home page.
 */

$content = $smarty->fetch("index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("body_id", "body_home");
$smarty->assign("page_title", "Running Site");
$smarty->display("master.tpl");
?>