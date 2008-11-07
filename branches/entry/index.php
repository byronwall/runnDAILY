<?php
require("lib/config.php");

$content = $smarty->fetch("index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Running Site");
$smarty->display("master.tpl");
?>