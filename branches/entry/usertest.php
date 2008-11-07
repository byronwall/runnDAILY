<?php
require("lib/config.php");

$mysqli = database::getDB();

$stmt = $mysqli->prepare("SELECT routes.name, id FROM routes") or die("error:".$stmt->error);

$stmt->execute() or die("error:".$stmt->error);
$stmt->store_result();

while ($row = $stmt->fetch_assoc()) {
	echo $row["id"];
	echo $row["name"];
}


?>
