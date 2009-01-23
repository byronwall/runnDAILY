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
}
Page::redirect("/admin/user.php");
?>