<?php
require("../lib/config.php");
$format = (isset($_GET["format"]))?$_GET["format"]:"html";

$parser = new SqlParser(true, 10, 0);
$parser->addCondition(new SqlRangeCondition("r_distance"));
$parser->addCondition(new SqlRangeCondition("r_creation", "FROM_UNIXTIME", "strtotime"));
$parser->addCondition(new SqlLikeCondition("u_username"));
$parser->addCondition(new SqlLikeCondition("r_name"));
$parser->addCondition(new SqlEqualCondition("u_uid"));
$parser->setData($_GET);

$stmt = Database::getDB()->prepare("
	SELECT *
	FROM routes
	JOIN users ON u_uid = r_uid
	WHERE 
		{$parser->getSQL()}
");

$parser->bindParamToStmt($stmt);
$stmt->execute();
$stmt->store_result();

$routes = array();
while($row = $stmt->fetch_assoc()){
	$routes[] = new Route($row);
}

$smarty->assign("routes", $routes);
$smarty->assign("query", $parser->getQueryString(true, true));
if($format == "ajax"){
	echo $smarty->fetch("routes/parts/route_list.tpl");
}
else{	
	$smarty->display_master("routes/browse.tpl");
}
?>