<?php
require("config.php");

if(!isset($_POST["f_uid"])){
	echo json_encode(false);
	exit;
}

$friend_uid = $_POST["f_uid"];

if(!isset($_POST["action"])){
	echo json_encode(false);
	exit;
}

switch($_POST["action"]){
	case "add":
		$is_added = $_SESSION["userData"]->addFriend($friend_uid);
		echo json_encode($is_added);
		exit;
		break;
	case "remove":
		$is_removed = $_SESSION["userData"]->removeFriend($friend_uid);
		echo json_encode($is_removed);
		exit;
		break;
}
?>