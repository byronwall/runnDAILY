<?php
require("config.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "logout":
			$user->logout();

			header("location: http://".$_SERVER['SERVER_NAME']);
			exit;
			break;
		case "login":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$remember = isset($_POST["remember"])?$_POST["remember"]:false;
			
			if($user->login($username, $password, $remember)){
				header("location: http://".$_SERVER['SERVER_NAME']);
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
				header("location: http://".$_SERVER['SERVER_NAME']);
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