<?php
require("config.php");

if(!isset($_POST["action"])){
	die("no action");
}
$action = $_POST["action"];

if(!isset($_POST["t_tid"])){
	header("location: http://". $_SERVER["SERVER_NAME"]. "/training");
	exit;
}

$t_item = TrainingLog::fromFetchAssoc($_POST);

switch ($action){
	case "edit":
		if($t_item->updateItem() ){
			header("location: http://". $_SERVER["SERVER_NAME"]. "/training/view.php?tid=".$t_item->tid );
			exit;
		}
		header("location: http://". $_SERVER["SERVER_NAME"]. "/training/manage.php?tid=".$t_item->tid );
		exit;
		break;
	case "delete":
		if($t_item->deleteItemSecure()){
			header("location: http://". $_SERVER["SERVER_NAME"]. "/training/");
			exit;
		}
		header("location: http://". $_SERVER["SERVER_NAME"]. "/training/manage.php?tid=".$t_item->tid );
		exit;
		break;
}

?>