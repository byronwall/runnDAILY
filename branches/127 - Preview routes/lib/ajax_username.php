<?php
require("config_class.php");

$username = $_GET["username"];

if(User::getUserExists($username)){
	echo json_encode(false);
	exit;
}
else{
	echo json_encode(true);
	exit;
}


?>