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
				$refer = "http://" . $_SERVER["SERVER_NAME"];
				if(isset($_SESSION["login_redirect"])){
					$refer = $_SESSION["login_redirect"];
					unset($_SESSION["login_redirect"]);
				}

				header("location: ".$refer);
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

			if(User::createUser($username, $password)){
				header("location: http://".$_SERVER['SERVER_NAME']);
				exit;
			}
			else{
				header("location: http://".$_SERVER['SERVER_NAME']."/register.php");
				exit;
			}
			break;
		case "activate":
			$uid = $_GET["uid"];
			$hash = $_GET["hash"];

			if(User::activateUser($uid, $hash)){
				echo "active";
				User::loginSystem(User::fromUid($uid));
			}
			header("location: http://". $_SERVER["SERVER_NAME"]."/index.php");
			exit;
			break;
	}
}
else{
	die("there needs to be an action");
}

?>