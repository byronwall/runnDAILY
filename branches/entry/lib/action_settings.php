<?php
require("config.php");
/*
 * This page is the helper page that will update user settings.
 */
var_dump($_POST);
var_dump($user);
var_dump($_SESSION);

$user->location_lat = $_POST["user_home_lat"];
$user->location_lng = $_POST["user_home_lng"];

if($user->updateUserDetails()){
	header("Location: http://". $_SERVER['SERVER_NAME']);
	exit;
}
else{
	header("Location: http://". $_SERVER['SERVER_NAME']. "/settings.php");
	exit;
}

?>