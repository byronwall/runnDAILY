<?php
class Controller_User{
	public function login(){
		RoutingEngine::setPage("runnDAILY User Login", "PV__400");
		$username = $_POST["username"];
		$password = $_POST["password"];
		$remember = isset($_POST["remember"])?$_POST["remember"]:false;

		if(User::login($username, $password, $remember)){
			$refer = "/";
			if(isset($_SESSION["login_redirect"])){
				$refer = $_SESSION["login_redirect"];
				unset($_SESSION["login_redirect"]);
			}
			Notification::add("You are logged in now.");
			Page::redirect($refer);
		}
		else{
			Notification::add("We were not able to log you in.");
			Page::redirect("/login");
		}
	}
	public function logout(){
		RoutingEngine::setPage("runnDAILY User Logout", "PV__400");
		User::logout();
		Notification::add("You are now logged out.");
		Page::redirect("/");
	}
	public function register(){
		RoutingEngine::setPage("runnDAILY User Register", "PV__400");
		$user = new User($_POST);

		$user->password = md5($user->password);
		$user->cookie_hash = md5(time());
		
		if($user->create()){
			if(isset($_FILES['user_img'])){
				$updir = PUBLIC_ROOT . "/img/user/";
				$path_part = pathinfo($_FILES['user_img']['name']);
				$upfile = (User::$current_user->uid % 100) . "/" . User::$current_user->uid . "." . $path_part["extension"];
				if (move_uploaded_file($_FILES['user_img']['tmp_name'], $updir . $upfile)){
					$user->updateImage($upfile);
				}
			}
			
			
			$_SESSION["userData"] = $user;
			Notification::add("Your account has been created.");
			Page::redirect("/");
		}
		else{
			Notification::add("There was an error creating your account.  Try again please.");
			Page::redirect("/register");
		}
	}
	public function activate(){
		//TODO: Implement this action.
		$uid = $_GET["uid"];
		$hash = $_GET["hash"];

		if(User::activateUser($uid, $hash)){
			echo "active";
			User::loginSystem(User::fromUid($uid));
		}
		Page::redirect("/index");
	}
	public function update(){
		RoutingEngine::setPage("runnDAILY User Update", "PV__300");
		$format = array_safe($_POST, "format", "html");
		$user = new User($_POST);
		if($user->updateUserInDB()){
			if($format == "ajax"){
				exit("success");
			}
		}
		Page::redirect("/admin/user");
	}
	
	public function action_settings(){
		RoutingEngine::setPage("runnDAILY User Settings", "PV__300");
		User::$current_user->refreshDetails($_POST);
		
		if(isset($_POST["u_password"])){
			User::$current_user->password = md5(User::$current_user->password);
		}
		
		if(User::$current_user->updateUserDetails() || User::$current_user->saveAllSettings()){
			Notification::add("Your settings have been updated.");
		}
		else{
			Notification::add("There was an error.  Please try again.");
		}
		Page::redirect("/settings");
	}
	public function check_exists(){
		RoutingEngine::setPage("runnDAILY User Exists", "PV__400");
		$username = $_GET["u_username"];

		if(User::getUserExists($username)){
			echo json_encode(false);
			exit;
		}
		else{
			echo json_encode(true);
			exit;
		}
	}
	function action_map_settings(){
		RoutingEngine::setPage("runnDAILY User Map Settings", "PV__300");
		User::$current_user->refreshDetails($_POST);
		User::$current_user->saveSetting("map_settings");
		
		exit;
	}
	function ajax_remove_notification(){
		RoutingEngine::setPage("runnDAILY User Remove Notification", "PV__300");
		if(!isset($_POST["id"])) return false;
		
		$id = $_POST["id"];
		Notification::remove($id);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax(true);
	}
}
?>