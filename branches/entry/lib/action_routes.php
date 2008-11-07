<?php
require("settings.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "save":
			$distance = $_POST["distance"];
			$points= $_POST["points"];
			$comments = $_POST["comments"];
			$name = $_POST["routeName"];

			$route = new Route();
			$good= $route->createNewRoute($_SESSION["userData"], $distance, $points, $comments, $name);
				
			if($good){
				header("location: http://".$_SERVER['SERVER_NAME']."/maps");
				exit;
			}
				
			break;
	}
}
else{
	die("There needs to be an action");
}
?>