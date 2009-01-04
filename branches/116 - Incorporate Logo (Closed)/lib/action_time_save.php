<?php
require("config.php");

var_dump($_POST);

$seconds = $_POST["time"];
$date = date("Y-m-d H:i:s", strtotime($_POST["date"]));
$distance = $_POST["distance"];
$route_id = $_POST["route_id"];

$stmt = database::getDB()->prepare("INSERT INTO training_times(t_uid, t_rid, t_time, t_distance, t_date) VALUES(?,?,?,?,?)");
$stmt->bind_param("iidds", $user->userID, $route_id, $seconds, $distance, $date) or die($stmt->error);

$stmt->execute() or die($stmt->error);

echo $stmt->affected_rows;

$stmt->close();

header("location: http://". $_SERVER['SERVER_NAME']."/training");
exit;
?>