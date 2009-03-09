<?php
class user_controller{
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
			Page::redirect($refer);
		}
		else{
			Page::redirect("/login");
		}
	}
	public function logout(){
		User::logout();
		Page::redirect("/");
	}
	public function register(){
		$user = new User($_POST);

		$user->password = md5($user->password);
		
		if($user->create()){
			$_SESSION["userData"] = $user;
			Page::redirect("/");
		}
		else{
			var_dump($GLOBALS);
			var_dump($user);
			die;
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
		$user->location_lat = $_POST["user_home_lat"];
		$user->location_lng = $_POST["user_home_lng"];
		$user->email = $_POST["u_email"];
		
		if($user->updateUserDetails()){
			Page::redirect("/");
		}
		else{
			Page::redirect("/settings");
		}
	}
	public function check_exists(){
		$username = $_GET["username"];

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
}
?>