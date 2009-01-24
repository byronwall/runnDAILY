<?php
require("config.php");

if(!isset($_POST["action"])){
	Page::redirect("/admin/users.php");
}
$format = array_safe($_POST, "format", "html");

switch($_POST["action"]){
	case "update":
		$user = new User($_POST);
		if($user->updateUserInDB()){
			if($format == "ajax"){
				exit("success");
			}
		}
		
		
		break;
	case "delete":
		$user = new User($_POST);

		$success = $user->deleteUser();

		if($success) exit("deleted");
		else exit("did not delete");
		break;
}
Page::redirect("/admin/user.php");
?>