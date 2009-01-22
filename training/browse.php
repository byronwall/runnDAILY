<?php
require("../lib/config.php");

$format = (isset($_GET["format"]))?$_GET["format"]:"html";

//SQL query code
$parser = new SqlParser(true, 5, 0);
$parser->addCondition(new SqlRangeCondition("t_distance"));
$parser->addCondition(new SqlRangeCondition("t_date", "FROM_UNIXTIME", "strtotime"));
$parser->addCondition(new SqlRangeCondition("t_time", "", "TrainingLog::getSecondsFromFormat"));
$parser->addCondition(new SqlLikeCondition("u_username"));
$parser->addCondition(new SqlEqualCondition("u_uid"));
$parser->setData($_GET);

$stmt = Database::getDB()->prepare("
	SELECT *
	FROM training_times
	JOIN users ON u_uid = t_uid
	WHERE 
		{$parser->getSQL()}
");

$parser->bindParamToStmt($stmt);
$stmt->execute();
$stmt->store_result();

$t_items = array();
while($row = $stmt->fetch_assoc()){
	$t_items[] = new TrainingLog($row);
}

$smarty->assign("t_items", $t_items);
$smarty->assign("query", $parser->getQueryString(true, true));
//END

if($format == "ajax"){
	echo $smarty->fetch("training/parts/item_list.tpl");
}
else{
	$smarty->display_master("training/browse.tpl");
}
?>