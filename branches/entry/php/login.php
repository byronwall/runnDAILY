<?php
require("settings.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "logout":
			$DEBUG[]="loggin out";
			$user->logout();

			header("location: ".$SETTINGS["address"]);
			exit;
			break;
		case "login":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$remember = $_POST["remember"];

			if($user->login($username, $password, $remember)){
				header("location: ".$SETTINGS["address"]);
				exit;
			}
			else{
				die("log in failed");
			}
			break;
		case "register":
			$username = $_POST["username"];
			$password = $_POST["password"];

			if($user->createUser($username, $password)){
				header("location: ".$SETTINGS["address"]);
				exit;
			}
			else{
				die("error creating new user");
			}
			break;
	}
}
else{
	die("there needs to be an action");
}

?>