<?php
if(!isset($_GET["common"])){
	exit;
}
$smarty->assign("content", $smarty->fetch("help/_pages/{$_GET["common"]}.tpl"));
?>