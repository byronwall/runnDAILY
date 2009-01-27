<?php
require("../lib/config.php");

$result = Database::getDB()->query("
	SELECT * FROM messages as m
	LEFT JOIN users ON u_uid = m_uid_from
	WHERE m.m_uid_to = 0
");
$msgs = array();

while($row = $result->fetch_assoc()){
	$msgs[] = new Message($row);
}

$result->close();

$smarty->assign("message", $msgs);
$smarty->display_master("admin/feedback.tpl");
?>