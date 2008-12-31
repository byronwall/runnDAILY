<?php
require("config.php");

if(!isset($_POST["action"])){
	die("no action");
}
$action = $_POST["action"];

if(!isset($_POST["tid"])){
	header("location: http://". $_SERVER["SERVER_NAME"]. "/training");
	exit;
}
$tid = $_POST["tid"];

switch ($action){
	case "edit":
		break;
	case "delete":
		if(TrainingLog::removeItemFromDB($tid, $user->userID)){
			header("location: http://". $_SERVER["SERVER_NAME"]. "/training/");
			exit;
		}
		break;
}

?>