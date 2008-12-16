<?php
require("config.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "logout":
			User::logout();
			header("location: http://".$_SERVER['SERVER_NAME']);
			exit;
			break;
		case "login":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$remember = isset($_POST["remember"])?$_POST["remember"]:false;
			
			if(User::login($username, $password, $remember)){
				$refer = (isset($_POST["refer"]))?$_POST["refer"]:"";
				
				header("location: http://".$_SERVER['SERVER_NAME'].$refer);
				exit;
			}
			else{
				header("location: http://". $_SERVER["SERVER_NAME"]."/login.php");
				exit;
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