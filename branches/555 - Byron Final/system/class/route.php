<?php
class Route extends Object{
	public $distance;
	public $points;
	public $description;
	public $name;
	public $id;
	public $start_lat;
	public $start_lng;
	public $creation;
	public $uid;
	public $gid;
	public $img_src;

	public $training_count;
	public $user;
	
	function __construct($arr = null, $arr_pre = "r_"){
		parent::__construct($arr, $arr_pre);
		
		$this->creation = strtotime($this->creation);
	}

	/**
	 * Function used to get the actual data that is held for the encoded
	 * poyline.  This function is called to generate the URL of the image
	 * generation page.
	 *
	 * @return string: the encoded polyline
	 */
	function getEncodedString(){
		$encoded = json_decode($this->points);

		return $encoded->points;
	}

	function copy(){
		var_dump($this);
		$db_route = Route::fromRouteIdentifier($this->id);
		var_dump($db_route);
		$db_route->name = $this->name;
		
		if($db_route->createRoute()){
			$this->id = $db_route->id;
			return true;
		}
		return false;
	}
	
	static function getPolyline($rid){
		$stmt = Database::getDB()->prepare("
			SELECT r_points
			FROM routes
			WHERE r_id = ?
		");
		$stmt->bind_param("i", $rid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$stmt->close();
		
		return $row["r_points"];
	}
	
	/**
	 * Finds the rotues for a given user.  Has parameters for a paged result.
	 *
	 * @param int $uid		uid for user in search
	 * @param $count		number of results to return (max)
	 * @param $page			number of page of results (0 indexed)
	 * @return array		array containing Route objects
	 */
	public static function getRoutesForUser($uid, $count = 10, $page = 0){
		$limit_lower = $page * $count;
		$limit_upper = $page * $count + $count;

		$stmt = Database::getDB()->prepare("SELECT * FROM routes WHERE r_uid=? ORDER BY r_creation DESC LIMIT ?,?") or die("error:".$stmt->error);
		$stmt->bind_param("iii", $uid, $limit_lower, $limit_upper) or die("error:".$stmt->error);
		$stmt->execute() or die("error:".$stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = new Route($row);
		}

		$stmt->close();
		return $route_list;
	}

	/**
	 * Returns an array of the all the routes in the database.
	 * This function will soon be deprecated by more specific functions.
	 *
	 * @return array of Route objects
	 */
	public static function getAllRoutes(){
		$stmt = Database::getDB()->prepare("SELECT * FROM routes LIMIT 20");

		$stmt->execute();
		$stmt->store_result();

		$routes = array();

		while($row = $stmt->fetch_assoc()){
			$routes[] = new Route($row);
		}

		$stmt->close();

		return $routes;
	}

	/**
	 * Returns the route with the given ID.
	 *
	 * @param int $id : the ID of the route to be fetched
	 * @return Route : a populated Route object with the details
	 */
	public static function fromRouteIdentifier($id){
		$stmt = Database::getDB()->prepare("
			SELECT r.*, u_username
			FROM routes as r, users as u
			WHERE
				r.r_id = ? AND
				r.r_uid = u.u_uid
		");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$stmt->close();

		$route = new Route($row);
		$route->data["u_username"] = $row["u_username"];
		
		return $route;
	}


	/**
	 * Returns a list of routes that are withing a given area.  This function is intended
	 * to be called for getting the routes within a given map area.  Assumes that the points
	 * are not wanted yet (quick AJAX calls).
	 *
	 * @param float $ne_lat
	 * @param float $ne_lng
	 * @param float $sw_lat
	 * @param float $sw_lng
	 * @return Array : an array of Route objects with the appropriate data
	 */
	public static function getRoutesInBox($ne_lat, $ne_lng, $sw_lat, $sw_lng){
		$stmt = Database::getDB()->prepare("SELECT * FROM routes WHERE r_start_lat BETWEEN ? AND ? AND r_start_lng BETWEEN ? AND ? LIMIT 10");
		$stmt->bind_param("dddd", $sw_lat, $ne_lat, $sw_lng, $ne_lng);

		$stmt->execute();
		$stmt->store_result();

		$routes_out = array();

		while($row = $stmt->fetch_assoc()){
			$routes_out[] = new Route($row);
		}

		$stmt->close();

		return $routes_out;
	}

	public static function deleteRouteSecure($rid, $uid){
		$stmt = Database::getDB()->prepare("
			DELETE routes
			FROM routes, users
			WHERE
				routes.r_id = ? AND
				routes.r_uid = users.u_uid AND
				users.u_uid = ?
		");
		$stmt->bind_param("ii", $rid, $uid);
		$stmt->execute();
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();
		if($rows == 1){
			Log::insertItem($uid, 101, null, $rid, null, null);
			return Route::_removeImage($rid);
		}
		return false;
	}

	public function updateRoute(){
		$stmt = Database::getDB()->prepare("
			UPDATE routes
			SET
				r_name = ?,
				r_creation = NOW(),
				r_points = ?,
				r_distance = ?,
				r_start_lat = ?,
				r_start_lng = ?,
				r_description = ?
			WHERE
				r_id = ?
		");
		$stmt->bind_param("ssdddsi", $this->name, $this->points, $this->distance, $this->start_lat, $this->start_lng, $this->description, $this->id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			Log::insertItem(User::$current_user->uid, 102, null, $this->id, null, null);
			$this->_storeImage();
			return true;
		}
		return false;
	}
	public function createRoute(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO routes
			SET
				r_name = ?,
				r_creation = NOW(),
				r_points = ?,
				r_distance = ?,
				r_start_lat = ?,
				r_start_lng = ?,
				r_description = ?,
				r_uid = ?
		");
		$stmt->bind_param("ssdddsi", $this->name, $this->points,
			$this->distance, $this->start_lat, $this->start_lng,
			$this->description, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();

		if($rows == 1){
			$this->id = $ins_id;
			Log::insertItem(User::$current_user->uid, 100, null, $this->id, null, null);
			
			return $this->_storeImage();
		}
		return false;
	}
	private static function _removeImage($rid){
		$img_src = ($rid % 100) . "/" . $rid . ".png";
		$path = PUBLIC_ROOT . "/img/route/" . $img_src;
		return unlink($path);		
	}
	private function _storeImage(){
		$img_src = ($this->id % 100) . "/" . $this->id . ".png";
		$path = PUBLIC_ROOT . "/img/route/" . $img_src;
		if($this->_createRouteImage($path)){
			$this->img_src = $img_src;
			$stmt = Database::getDB()->prepare("
				UPDATE routes
				SET
					r_img_src = ?
				WHERE
					r_id = ?
			");
			$stmt->bind_param("si", $this->img_src, $this->id);
			$stmt->execute() or die($stmt->error);
			$stmt->store_result();
			
			$rows = $stmt->affected_rows;
			$stmt->close();
			
			return $rows == 1;
		}
		else{
			die("error storing image");
		}
		return false;
	}
		
	private function _getBoundingBox($points){
		$point_max["x"] = max($points["x"]);
		$point_max["y"] = max($points["y"]);
		$point_min["x"] = min($points["x"]);
		$point_min["y"] = min($points["y"]);
	
		$width = ($point_max["x"] - $point_min["x"]) / 2;
		$height = ($point_max["y"] - $point_min["y"]) / 2;
		$center = array("x"=>($point_max["x"] + $point_min["x"]) / 2, "y"=>($point_max["y"] + $point_min["y"]) / 2);
	
		return array("height"=>$height, "width"=>$width, "center"=>$center);
	}
	public function _createRouteImage($path){
		$encoded = json_decode($this->points)->points;
		$distance = $this->distance;
		
		$im_width = 100;
		$im_height = 100;
		
		$padding = 0.85;
		
		$point_arr = $this->_decodePolylineToArray($encoded);
		
		$bg = imagecreatefrompng(PUBLIC_ROOT."/img/earth.png");
		
		$im = imagecreatetruecolor($im_width, $im_height) or die('Cannot Initialize new GD image stream');
		
		imageSaveAlpha($bg, true);
		ImageAlphaBlending($im, true);
		ImageAntiAlias($im, true);
		
		$black = imagecolorallocatealpha($im, 0x00, 0x00, 0x00, 0);
		$clear = imagecolorallocatealpha($im, 200, 200, 200, 127);
		$line_color = imagecolorallocatealpha($im, "0x00", "0x3c", "0xff", 0);
		imagefill($im, 0, 0, $clear);
		
		$boundingBox = $this->_getBoundingBox($point_arr);
		
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
		$font = PUBLIC_ROOT. "/font/arial.ttf";
		
		// Add some shadow to the text
		imagettftext($im, 10, 0, $im_width * 0.05, $im_height * 0.95, $black, $font, $distance." mi");
		
		imagecopy($bg, $im, 0, 0, 0, 0, $im_width, $im_height);
		//imagecopy($shadow, $bg, 7, 6, 0, 0, $im_width, $im_height);
		
		$result = imagepng($bg, $path);
		imagedestroy($im);
		imagedestroy($bg);
		
		return $result;
	}
	
	
	private function _decodePolylineToArray($encoded)
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

	public function getTrainingCount(){
		if($this->training_count) return $this->training_count;
		
		$stmt = Database::getDB()->prepare("
			SELECT COUNT(*) as total FROM training_times WHERE t_rid = ?
		");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		$stmt->close();
		
		$this->training_count = $row["total"];
		
		return $row["total"];
	}
	
	public function getCanEdit(){
		return $this->getTrainingCount() == 0;
	}
	public function getHasParent(){
		return $this->rid_parent != null;
	}
	public function getIsOwner($uid){
		return $this->uid = $uid;
	}
	/**
	 * @param $uid
	 * @param $count
	 * @return unknown_type
	 */
	static function getRoutesForUserInArray($uid, $count = 50){
		$stmt = Database::getDB()->prepare("
			SELECT r_id, r_name, r_start_lat, r_start_lng, r_distance, r_creation
			FROM routes
			WHERE
				r_uid = ?
			ORDER BY
				r_creation DESC
			LIMIT ?
		");
		$stmt->bind_param("ii", $uid, $count);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$results = array();
		while($row = $stmt->fetch_assoc()){
			$results[$row["r_id"]] = $row;
		}
		
		$stmt->close();
		return $results;
	}
}
?>