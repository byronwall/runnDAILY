<?php
require("lib/config.php");

$id = 1;

$mysqli = database::getDB();
$stmt = $mysqli->prepare("SELECT * FROM routes WHERE r_uid = ?") or die("error:".$stmt->error);
$stmt->bind_param("i", $id);
$stmt->execute() or die("error:".$stmt->error);
$stmt->store_result();

while ($row = $stmt->fetch_assoc()) {
	echo $row["r_id"];
	echo $row["r_name"];
}


?>