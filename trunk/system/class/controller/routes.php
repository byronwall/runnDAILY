<?php
class Controller_Routes{
	public function index(){
		$routes = Route::getRoutesForUserInArray(User::$current_user->uid, 50);
		$routes_js = json_encode_null($routes);
		
		RoutingEngine::getSmarty()->assign("routes", $routes);
		RoutingEngine::getSmarty()->assign("routes_js", $routes_js);
	}
	public function ajax_route_data(){
		if(!isset($_GET["rid"])){
			RoutingEngine::returnAjax(false);
		}
		
		$route = Route::getPolyline($_GET["rid"]);
		
		//note that this already is JSON
		RoutingEngine::returnAjax($route, false);
	}
	public function view(){
		if(!isset($_GET["rid"])){
			Page::redirect("/routes/");
		}
		$id = $_GET["rid"];
		$route = Route::fromRouteIdentifier($id);
		//get training types for create new training modal
		
		$stmt = Database::getDB()->prepare("
			SELECT t_type_id, t_type_name
			FROM training_types
		");
		$stmt->execute();
		$stmt->store_result();
		$types = array();
		while($row = $stmt->fetch_assoc()){
			$types[] = array("id"=>$row["t_type_id"], "name"=>$row["t_type_name"]);
		}
		$stmt->close();
		
		RoutingEngine::getSmarty()->assign("t_types", $types);
		RoutingEngine::getSmarty()->assign("route_view", $route);
	}
	public function browse(){
		$format = (isset($_GET["format"]))?$_GET["format"]:"html";
	
		$parser = new Sql_Parser(true, 10, 0);
		$parser->addCondition(new Sql_RangeCondition("r_distance"));
		$parser->addCondition(new Sql_RangeCondition("r_creation", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new Sql_LikeCondition("u_username"));
		$parser->addCondition(new Sql_LikeCondition("r_name"));
		$parser->addCondition(new Sql_EqualCondition("u_uid"));
		$parser->setData($_GET);
		
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM routes
			JOIN users ON u_uid = r_uid
			WHERE
				{$parser->getSQL()}
		");
		
		$parser->bindParamToStmt($stmt);
		$stmt->execute();
		$stmt->store_result();
		
		$routes = array();
		while($row = $stmt->fetch_assoc()){
			$routes[] = new Route($row);
		}
		
		RoutingEngine::getSmarty()->assign("routes", $routes);
		RoutingEngine::getSmarty()->assign("query", $parser->getQueryString(true, true));
		if($format == "ajax"){
			exit(RoutingEngine::getSmarty()->fetch("routes/parts/route_list.tpl"));
		}
	}
	public function create(){
		if(isset($_GET["rid"])){
			$route = Route::fromRouteIdentifier($_GET["rid"]);
			RoutingEngine::getSmarty()->assign("route_edit", $route);
			RoutingEngine::getSmarty()->assign("is_edit", true);

			if(isset($_GET["mode"])){
				$isCopy = $_GET["mode"] == "copy";
				RoutingEngine::getSmarty()->assign("isCopy", $isCopy);
			}
		}
		RoutingEngine::getSmarty()->assign("body_id", "map_create");
	}
	public function action_create(){
		$route = new Route($_POST);
		if($route->createRoute()){
			Page::redirect("/routes/view?rid={$route->id}");
		}
	}
	public function action_delete(){
		$rid = $_POST["r_rid"];
		$uid = User::$current_user->uid;

		if(Route::deleteRouteSecure($rid, $uid)){
		Notification::add("Route was deleted.", true);
			Page::redirect("/routes/");
		}
		Page::redirect("/routes/view?rid={$rid}");
	}
	public function action_edit(){
		$route = new Route($_POST);
		if($route->updateRoute()){
			Page::redirect("/routes/view?rid={$route->id}");
		}
		die("error updating?");
	}
	public function image(){
		$encoded = (isset($_GET["encoded"]))? $_GET["encoded"]: die();
		$distance = (isset($_GET["distance"]))? $_GET["distance"]: die();
		
		$im_width = 100;
		$im_height = 100;
		
		$padding = 0.85;
		
		$point_arr = decodePolylineToArray($encoded);
		
		$bg = imagecreatefrompng(PUBLIC_ROOT."/img/earth.png");
		
		$im = imagecreatetruecolor($im_width, $im_height) or die('Cannot Initialize new GD image stream');
		
		imageSaveAlpha($bg, true);
		ImageAlphaBlending($im, true);
		ImageAntiAlias($im, true);
		
		$black = imagecolorallocatealpha($im, 0x00, 0x00, 0x00, 0);
		$clear = imagecolorallocatealpha($im, 200, 200, 200, 127);
		$line_color = imagecolorallocatealpha($im, "0x00", "0x3c", "0xff", 0);
		imagefill($im, 0, 0, $clear);
		
		$boundingBox = getBoundingBox($point_arr);
		
		$height = $boundingBox["height"];
		$width = $boundingBox["width"];
		
		$height = $width = max(array($height, $width));
		
		$center_point = $boundingBox["center"];
		
		$normal_points = array();
		$normal_scaled = array();
		
		$i = 0;
		while($i < count($point_arr["x"])){
			$x_dist = $point_arr["x"][$i] - $center_point["x"];
			$y_dist = $point_arr["y"][$i] - $center_point["y"];
		
			$x_norm = $x_dist / $width * $padding;
			$y_norm = $y_dist/$height * $padding;
		
			$normal_points[] = array("x"=>$x_norm, "y"=>$y_norm);
		
			$x_scaled = ($x_norm+1)*$im_width/2;
			$y_scaled = ($y_norm+1)*$im_height/2;
		
			$normal_scaled[] = array("x"=>$x_scaled, "y"=>$y_scaled);
		
			$i++;
		}
		
		for($i = 1; $i < count($normal_points); $i++){
			imageline($im, $normal_scaled[$i-1]["x"], $normal_scaled[$i-1]["y"], $normal_scaled[$i]["x"], $normal_scaled[$i]["y"],$line_color);
		}
		
		//imagestring($im, 3, $im_width * 0.3, $im_height * 0.75, $distance." mi", $black);
		
		// The text to draw
		//$text = 'Testing...';
		// Replace path by your own font path
		$font = '../public/font/arial.ttf';
		
		// Add some shadow to the text
		imagettftext($im, 10, 0, $im_width * 0.05, $im_height * 0.95, $black, $font, $distance." mi");
		
		imagecopy($bg, $im, 0, 0, 0, 0, $im_width, $im_height);
		//imagecopy($shadow, $bg, 7, 6, 0, 0, $im_width, $im_height);
		
		header ("Content-type: image/png");
		imagepng($bg);
		//imagedestroy($shadow);
		imagedestroy($im);
		imagedestroy($bg);
		
		exit;
	}
}
function getBoundingBox($points){
	$point_max["x"] = max($points["x"]);
	$point_max["y"] = max($points["y"]);
	$point_min["x"] = min($points["x"]);
	$point_min["y"] = min($points["y"]);

	$width = ($point_max["x"] - $point_min["x"]) / 2;
	$height = ($point_max["y"] - $point_min["y"]) / 2;
	$center = array("x"=>($point_max["x"] + $point_min["x"]) / 2, "y"=>($point_max["y"] + $point_min["y"]) / 2);

	return array("height"=>$height, "width"=>$width, "center"=>$center);
}

function decodePolylineToArray($encoded)
{
	$length = strlen($encoded);
	$index = 0;
	$points = array();
	$lat = 0;
	$lng = 0;
	while ($index < $length)
	{
		$b = 0;
		$shift = 0;
		$result = 0;
		do
		{
			$b = ord(substr($encoded, $index++)) - 63;
			$result |= ($b & 0x1f) << $shift;
			$shift += 5;
		}
		while ($b >= 0x20);
		$dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
		$lat += $dlat;
		$shift = 0;
		$result = 0;
		do
		{
			$b = ord(substr($encoded, $index++)) - 63;
			$result |= ($b & 0x1f) << $shift;
			$shift += 5;
		}
		while ($b >= 0x20);
		$dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
		$lng += $dlng;
		$points["x"][] = $lng * 1e-5;
		$points["y"][] = -($lat * 1e-5);
	}

	return $points;
}
?>