<?php
require("config.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "logout":
			Log::insertItem($_SESSION["userData"]->userID, 202, null, null, null, null);
			User::logout();
			header("location: http://".$_SERVER['SERVER_NAME']);
			exit;
			break;
		case "login":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$remember = isset($_POST["remember"])?$_POST["remember"]:false;
			
			if(User::login($username, $password, $remember)){
				Log::insertItem($_SESSION["userData"]->userID, 201, null, null, null, null);
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
				Log::insertItem($_SESSION["userData"]->userID, 200, null, null, null, null);
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