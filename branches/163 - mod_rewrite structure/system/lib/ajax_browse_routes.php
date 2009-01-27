<?php
switch($_GET["action"]){
	case "list":
		$sw_lat = $_GET["sw_corner_lat"];
		$sw_lng = $_GET["sw_corner_lng"];

		$ne_lat = $_GET["ne_corner_lat"];
		$ne_lng = $_GET["ne_corner_lng"];

		$output = Route::getRoutesInBox($ne_lat, $ne_lng, $sw_lat, $sw_lng);

		echo json_encode($output);

		break;
	case "view":
		$id = $_GET["route_id"];
		
		$route = Route::fromRouteIdentifier($id);
		
		echo json_encode($route);
		
		break;
}
?>