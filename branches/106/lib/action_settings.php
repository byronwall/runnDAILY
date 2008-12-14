<?php
require("config.php");
/*
 * This page is the helper page that will update user settings.
 */

$user->location_lat = $_POST["user_home_lat"];
$user->location_lng = $_POST["user_home_lng"];
$user->u_email = $_POST["u_email"];

if($user->updateUserDetails()){
	header("Location: http://". $_SERVER['SERVER_NAME']);
	exit;
}
else{
	header("Location: http://". $_SERVER['SERVER_NAME']. "/settings.php");
	exit;
}

?>