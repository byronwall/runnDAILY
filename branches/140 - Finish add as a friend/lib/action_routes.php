<?php
require("config.php");

if(isset($_POST["action"])){
	switch ($_POST["action"]){
		case "save":
			$route = Route::fromFetchAssoc($_POST, true);
			
			if($route->createRoute()){
				header("location: http://".$_SERVER['SERVER_NAME']."/routes/view.php?id=".$route->id);
				exit;
			}

			break;
		case "edit":
			$route = Route::fromFetchAssoc($_POST, true);
			if($route->updateRoute()){
				header("location: http://".$_SERVER['SERVER_NAME']."/routes/view.php?id=".$route->id);
				exit;
			}
			die("error updating?");
				
			break;
		case "delete":
			$rid = $_POST["r_rid"];
			$uid = $_SESSION["userData"]->userID;

			if(Route::deleteRouteSecure($rid, $uid)){
				header("location: http://".$_SERVER['SERVER_NAME']."/routes/");
				exit;
			}
			header("location: http://".$_SERVER['SERVER_NAME']."/routes/view.php?id=".$rid);
			exit;

			break;
	}
}

header("location: http://".$_SERVER['SERVER_NAME']);
exit;
?>