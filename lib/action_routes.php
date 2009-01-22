<?php
require("config.php");

if(isset($_POST["action"])){
	switch ($_POST["action"]){
		case "save":
			$route = new Route($_POST);
			
			if($route->createRoute()){
				Page::redirect("/routes/view.php?id={$route->id}");
			}

			break;
		case "edit":
			$route = new Route($_POST);
			if($route->updateRoute()){
				Page::redirect("/routes/view.php?id={$route->id}");
			}
			die("error updating?");
				
			break;
		case "delete":
			$rid = $_POST["r_rid"];
			$uid = User::$current_user->uid;

			if(Route::deleteRouteSecure($rid, $uid)){
				Page::redirect("/routes/");
			}
			Page::redirect("/routes/view.php?id={$rid}");
			break;
	}
}
Page::redirect("/");
?>