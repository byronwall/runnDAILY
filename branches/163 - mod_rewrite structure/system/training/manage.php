<?php
if(isset($_GET["tid"])){
	$tid = $_GET["tid"];
	$item = TrainingLog::getItem($tid, true);
	$smarty->assign("t_item", $item);
}
?>