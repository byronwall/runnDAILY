<?php
require("config.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "logout":
			User::logout();
			Page::redirect("/");
			break;
		case "login":
			$username = $_POST["username"];
			$password = $_POST["password"];
			$remember = isset($_POST["remember"])?$_POST["remember"]:false;

			if(User::login($username, $password, $remember)){
				$refer = "/";
				if(isset($_SESSION["login_redirect"])){
					$refer = $_SESSION["login_redirect"];
					unset($_SESSION["login_redirect"]);
				}
				Page::redirect($refer);
			}
			else{
				Page::redirect("/login.php");
			}
			break;
		case "register":
			$username = $_POST["username"];
			$password = $_POST["password"];

			if(User::createUser($username, $password)){
				Page::redirect("/");
			}
			else{
				Page::redirect("/register.php");
			}
			break;
		case "activate":
			$uid = $_GET["uid"];
			$hash = $_GET["hash"];

			if(User::activateUser($uid, $hash)){
				echo "active";
				User::loginSystem(User::fromUid($uid));
			}
			Page::redirect("/index.php");
			break;
	}
}
else{
	die("there needs to be an action");
}

?>