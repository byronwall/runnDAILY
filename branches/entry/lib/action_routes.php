<?php
require("config.php");

if(isset($_REQUEST["action"])){
	switch ($_REQUEST["action"]){
		case "save":
			$distance = $_POST["distance"];
			$points= $_POST["points"];
			$comments = $_POST["comments"];
			$name = $_POST["routeName"];
			$start_lat = $_POST["start_lat"];
			$start_lng = $_POST["start_lng"];

			$route = new Route();
			$route_id = $route->createNewRoute($_SESSION["userData"], $distance, $points, $comments, $name, $start_lat, $start_lng);

			if($route_id){
				header("location: http://".$_SERVER['SERVER_NAME']."/routes/view.php?id=".$route_id);
				exit;
			}

			break;
	}
}

header("location: http://".$_SERVER['SERVER_NAME']);
exit;
?>