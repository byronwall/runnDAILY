<?php
require("../lib/config.php");

if(isset($_GET["tid"])){
	$tid = $_GET["tid"];
	$item = TrainingLog::getItem($tid, true);
	$smarty->assign("t_item", $item);
}

$smarty->display_master("training/manage.tpl");
?>