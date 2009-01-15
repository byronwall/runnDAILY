<?php
require("../lib/config.php");

if(!isset($_GET["format"])){
	header("location: http://{$_SERVER['SERVER_NAME']}/training/");
	exit;
}

if($_GET["format"] == "ajax"){
	$uid = $_GET["uid"];
	$page_no = $_GET["page"];

	$t_items = TrainingLog::getItemsForUserPaged($uid, 4, $page_no);

	$smarty->assign("t_items", $t_items);
	$smarty->assign("uid", $uid);
	$smarty->assign("page_no", $page_no+1);
	
	echo $smarty->fetch("training/parts/item_list.tpl");
}
?>