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
		$username = $_POST["username"];
		$password = $_POST["password"];

		if(User::createUser($username, $password)){
			Page::redirect("/");
		}
		else{
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
}
?>