<?php
require("../lib/config.php");

if(isset($_GET["tid"])){
	$tid = $_GET["tid"];
	$item = TrainingLog::getItem($tid, true);
	$smarty->assign("t_item", $item);
}

$content = $smarty->fetch("training/manage.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");

?>