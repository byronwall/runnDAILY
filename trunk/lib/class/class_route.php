<?php
class Route{

	var $distance;
	var	$points;
	var	$comments;
	var	$name;
	var $id;
	var $rid_parent;
	var $start_lat;
	var $start_lng;
	var $date_creation;
	var $uid;
	
	var $training_count;
	var $user;

	function __construct(){
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

	/**
	 * Returns the routes that were created by a specific user
	 *
	 * @param User $user : the user in question
	 * @return array of Route objects
	 */
	public static function getRoutesForUser($uid, $count = 10, $page = 0){
		$limit_lower = $page * $count;
		$limit_upper = $page * $count + $count;

		$stmt = database::getDB()->prepare("SELECT * FROM routes WHERE r_uid=? ORDER BY r_creation DESC LIMIT ?,?") or die("error:".$stmt->error);
		$stmt->bind_param("iii", $uid, $limit_lower, $limit_upper) or die("error:".$stmt->error);
		$stmt->execute() or die("error:".$stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = Route::fromFetchAssoc($row, true);
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
		$stmt = database::getDB()->prepare("SELECT * FROM routes LIMIT 20");

		$stmt->execute();
		$stmt->store_result();

		$routes = array();

		while($row = $stmt->fetch_assoc()){
			$routes[] = Route::fromFetchAssoc($row, true);
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
		$stmt = database::getDB()->prepare("
			SELECT *
			FROM routes as r, users as u
			WHERE
				r.r_id = ? AND
				r.r_uid = u.u_uid
		");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();

		if($row = $stmt->fetch_assoc()){
			$route = Route::fromFetchAssoc($row, true, true);
			$stmt->close();
			return $route;
		}
		else{
			$stmt->close();
			return false;
		}
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
		$stmt = database::getDB()->prepare("SELECT * FROM routes WHERE r_start_lat BETWEEN ? AND ? AND r_start_lng BETWEEN ? AND ? LIMIT 10");
		$stmt->bind_param("dddd", $sw_lat, $ne_lat, $sw_lng, $ne_lng);

		$stmt->execute();
		$stmt->store_result();

		$routes_out = array();

		while($row = $stmt->fetch_assoc()){
			$routes_out[] = Route::fromFetchAssoc($row);
		}

		$stmt->close();

		return $routes_out;
	}


	/**
	 * Creates a new Route object from the result of a database query.  It is assumed
	 * that the call is of the form SELECT *, or that the call will grab enough
	 * fields to justify calling this method.  Intended to provide a uniform spot
	 * for creating a route from the database without being repetitive.
	 *
	 * @param Array $row : an associative array containing the data
	 * @param bool $includePoints : whether or not to grab point data
	 * @return Route : the object created from the data
	 */
	public static function fromFetchAssoc($row, $includePoints = false, $includeUser = false){
		$route = new Route();
			
		$route->distance = (isset($row["r_distance"]))?$row["r_distance"]:null;
		$route->name = (isset($row["r_name"]))?$row["r_name"]:null;
		$route->start_lat = (isset($row["r_start_lat"]))?$row["r_start_lat"]:null;
		$route->start_lng = (isset($row["r_start_lng"]))?$row["r_start_lng"]:null;
		$route->id = (isset($row["r_id"]))?$row["r_id"]:null;
		$route->date_creation = (isset($row["r_creation"]))?$row["r_creation"]:null;
		$route->uid = (isset($row["r_uid"]))?$row["r_uid"]:null;
		$route->rid_parent = (isset($row["r_rid_parent"]))?$row["r_rid_parent"]:null;

		if($includePoints){
			$route->points = (isset($row["r_points"]))?$row["r_points"]:null;
		}
		if($includeUser){
			$route->user = User::fromFetchAssoc($row);
		}
			
		return $route;
	}

	public static function deleteRouteSecure($rid, $uid){
		$stmt = database::getDB()->prepare("
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
			return true;
		}
		return false;
	}

	public function updateRoute(){
		$stmt = database::getDB()->prepare("
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
		$stmt->bind_param("ssdddsi", $this->name, $this->points, $this->distance, $this->start_lat, $this->start_lng, $this->comments, $this->id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			Log::insertItem($_SESSION["userData"]->userID, 102, null, $this->id, null, null);
			return true;
		}
		return false;
	}
	public function createRoute(){
		$stmt = database::getDB()->prepare("
			INSERT INTO routes
			SET
				r_name = ?,
				r_creation = NOW(),
				r_points = ?,
				r_distance = ?,
				r_start_lat = ?,
				r_start_lng = ?,
				r_description = ?,
				r_uid = ?,
				r_rid_parent = ?
		");
		$stmt->bind_param("ssdddsii", $this->name, $this->points,
			$this->distance, $this->start_lat, $this->start_lng,
			$this->comments, $_SESSION["userData"]->userID, $this->rid_parent);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();

		if($rows == 1){
			$this->id = $ins_id;
			Log::insertItem($_SESSION["userData"]->userID, 100, null, $this->id, null, null);
			return true;
		}
		return false;
	}

	public function getTrainingCount(){
		if($this->training_count) return $this->training_count;
		
		$stmt = database::getDB()->prepare("
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
}
?>