<?php
require("../lib/config.php");

$content = $smarty->fetch("about/toc.tpl");

$smarty->assign("page_content", $content);
$smarty->display("master.tpl");
?>