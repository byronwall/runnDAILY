<?php
class routes_controller{
	public function index(){
		$recent_routes = Route::getRoutesForUser(User::$current_user->uid, 6);
		$recent_activity = Log::getRouteActivityForUser(User::$current_user->uid);
		$more_routes = Route::getRoutesForUser(User::$current_user->uid, 50);
		
		RoutingEngine::getSmarty()->assign("recent_route_list", $recent_routes);
		RoutingEngine::getSmarty()->assign("recent_activity_list", $recent_activity);
		RoutingEngine::getSmarty()->assign("all_route_list", $more_routes);
	}
	public function view(){
		if(!isset($_GET["rid"])){
			Page::redirect("/routes/");
		}
		$id = $_GET["rid"];
		$route = Route::fromRouteIdentifier($id);
		RoutingEngine::getSmarty()->assign("route_view", $route);
	}
	public function browse(){
		$format = (isset($_GET["format"]))?$_GET["format"]:"html";
	
		$parser = new SqlParser(true, 10, 0);
		$parser->addCondition(new SqlRangeCondition("r_distance"));
		$parser->addCondition(new SqlRangeCondition("r_creation", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new SqlLikeCondition("u_username"));
		$parser->addCondition(new SqlLikeCondition("r_name"));
		$parser->addCondition(new SqlEqualCondition("u_uid"));
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
			echo RoutingEngine::getSmarty()->fetch("routes/parts/route_list.tpl");
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
			Page::redirect("/routes/view.php?id={$route->id}");
		}
	}
	public function action_delete(){
		$rid = $_POST["r_rid"];
		$uid = User::$current_user->uid;

		if(Route::deleteRouteSecure($rid, $uid)){
			Page::redirect("/routes/");
		}
		Page::redirect("/routes/view.php?id={$rid}");
	}
	public function action_edit(){
		$route = new Route($_POST);
		if($route->updateRoute()){
			Page::redirect("/routes/view.php?id={$route->id}");
		}
		die("error updating?");
	}
	public function image(){
		$encoded = (isset($_GET["encoded"]))? $_GET["encoded"]: die();
		$distance = (isset($_GET["distance"]))? $_GET["distance"]: die();
		
		$im_width = 40;
		$im_height = 40;
		
		$padding = 0.85;
		
		$point_arr = decodePolylineToArray($encoded);
		
		$bg = imagecreatefrompng(PUBLIC_ROOT."/img/route_bg.png");
		
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
		
		imagestring($im, 2, 50, 85, $distance." mi", $black);
		
		//imagecopy($bg, $im, 5, 5, 0, 0, $im_width, $im_height);
		//imagecopy($shadow, $bg, 7, 6, 0, 0, $im_width, $im_height);
		
		header ("Content-type: image/png");
		imagepng($im);
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