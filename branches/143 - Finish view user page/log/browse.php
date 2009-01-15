<?php
require("../lib/config.php");

if(!isset($_GET["format"])){
	header("location: http://{$_SERVER['SERVER_NAME']}/");
	exit;
}

if($_GET["format"] == "ajax"){

	$uid = $_GET["uid"];
	$page_no = $_GET["page"];

	$logs = Log::getAllActivityForUserPaged($uid, 5, $page_no);

	$smarty->assign("logs", $logs);
	$smarty->assign("uid", $uid);
	$smarty->assign("page_no", $page_no+1);
	
	echo $smarty->fetch("log/log_list.tpl");
}
?>