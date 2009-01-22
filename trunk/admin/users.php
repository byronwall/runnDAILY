<?php
require("../lib/config.php");

$parser = new SqlParser(true, 10, 0);
$parser->addCondition(new SqlRangeCondition("u_date_access", "FROM_UNIXTIME", "strtotime"));
$parser->addCondition(new SqlLikeCondition("u_username"));
$parser->addCondition(new SqlLikeCondition("u_email"));
$parser->addCondition(new SqlEqualCondition("u_uid"));
$parser->addCondition(new SqlRangeCondition("u_type"));
$parser->setData($_GET);

$stmt = Database::getDB()->prepare("
	SELECT *
	FROM users
	WHERE 
		{$parser->getSQL()}
");

$parser->bindParamToStmt($stmt);
$stmt->execute();
$stmt->store_result();

$users = array();
while($row = $stmt->fetch_assoc()){
	$users[] = new User($row);
}

$smarty->assign("users", $users);

$smarty->display_master("admin/users.tpl");
?>