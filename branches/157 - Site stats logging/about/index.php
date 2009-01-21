<?php
require("../lib/config.php");

$content = $smarty->fetch("about/index.tpl");

$smarty->assign("page_content", $content);
$smarty->display("master.tpl");
?>