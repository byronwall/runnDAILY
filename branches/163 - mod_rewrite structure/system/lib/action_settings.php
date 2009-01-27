<?php
$user->location_lat = $_POST["user_home_lat"];
$user->location_lng = $_POST["user_home_lng"];
$user->email = $_POST["u_email"];

if($user->updateUserDetails()){
	Page::redirect("/");
}
else{
	Page::redirect("/settings.php");
}

?>