<?php
require("config.php");

$username = $_GET["username"];

if(User::fromUsername($username)){
	echo "taken";	
}
else{
	echo "available";
}


?>