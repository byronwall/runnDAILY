<?php
require("../lib/config.php");

$format = (isset($_GET["format"]))?$_GET["format"]:"html";

if($format == "ajax"){

	$uid = $_GET["uid"];
	$page_no = $_GET["page"];

	$logs = Log::getAllActivityForUserPaged($uid, 5, $page_no);

	$smarty->assign("logs", $logs);
	$smarty->assign("uid", $uid);
	$smarty->assign("page_no", $page_no+1);
	
	echo $smarty->fetch("log/log_list.tpl");
}
else{
	
}
?>