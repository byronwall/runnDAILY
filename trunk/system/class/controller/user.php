<?php
class Controller_User{
	public function login(){
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
		User::logout();
		Notification::add("You are now logged out.");
		Page::redirect("/");
	}
	public function register(){
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
		$uid = $_GET["uid"];
		$hash = $_GET["hash"];

		if(User::activateUser($uid, $hash)){
			echo "active";
			User::loginSystem(User::fromUid($uid));
		}
		Page::redirect("/index");
	}
	public function update(){
		$format = array_safe($_POST, "format", "html");
		$user = new User($_POST);
		if($user->updateUserInDB()){
			if($format == "ajax"){
				exit("success");
			}
		}
		Page::redirect("/admin/user");
	}
	
	public function delete(){
		$user = new User($_POST);

		$success = $user->deleteUser();

		if($success) exit("deleted");
		else exit("did not delete");
		Page::redirect("/admin/user");
	}
	public function action_settings(){
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
	public function action_update_modules(){
		$modules = $_POST["modules"];
		User::$current_user->routes_modules = implode(",", $modules["routes"]);
		User::$current_user->home_modules = implode(",", $modules["home"]);
		User::$current_user->training_modules = implode(",", $modules["training"]);
		User::$current_user->community_modules = implode(",", $modules["community"]);
		$stmt = Database::getDB()->prepare("
			UPDATE users_settings
			SET
				u_routes_modules = ?,
				u_home_modules = ?,
				u_training_modules = ?,
				u_community_modules = ?
			WHERE
				u_uid = ?
		");
		$stmt->bind_param("ssssi",User::$current_user->routes_modules,User::$current_user->home_modules,User::$current_user->training_modules,User::$current_user->community_modules,User::$current_user->uid);
		$stmt->execute();
		
		$stmt->close();
		
		Page::redirect("/modules");
	}
	function action_map_settings(){
		User::$current_user->refreshDetails($_POST);
		User::$current_user->saveSetting("map_settings");
		
		exit;
	}
	function ajax_remove_notification(){
		if(!isset($_POST["id"])) return false;
		
		$id = $_POST["id"];
		Notification::remove($id);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax(true);
	}
}
?>