<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */

$content = $smarty->fetch("training/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");
?>