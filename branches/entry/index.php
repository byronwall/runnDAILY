<?php
require("php/settings.php");

$smarty->assign("debugMode", 0);
$smarty->assign("debugData", $DEBUG);

$content = $smarty->fetch("index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Running Site");
$smarty->display('master.tpl');
?>