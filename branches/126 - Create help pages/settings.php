<?php
require("lib/config.php");

/*
 * This is the main index for the site.
 * It will either load the dashboard or a generic home page.
 */

$content = $smarty->fetch("settings.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Settings .::. Running Site");
$smarty->display("master.tpl");
?>