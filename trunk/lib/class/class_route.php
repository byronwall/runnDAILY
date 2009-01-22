<?php
class Route extends Object{
	public $distance;
	public $points;
	public $description;
	public $name;
	public $id;
	public $rid_parent;
	public $start_lat;
	public $start_lng;
	public $creation;
	public $uid;

	public $training_count;
	public $user;

	function __construct($arr = null, $arr_pre = "r_"){
		parent::__construct($arr, $arr_pre);
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
			SELECT *
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

		return new Route($row);
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
			return true;
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
				r_uid = ?,
				r_rid_parent = ?
		");
		$stmt->bind_param("ssdddsii", $this->name, $this->points,
			$this->distance, $this->start_lat, $this->start_lng,
			$this->description, User::$current_user->uid, $this->rid_parent);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();

		if($rows == 1){
			$this->id = $ins_id;
			Log::insertItem(User::$current_user->uid, 100, null, $this->id, null, null);
			return true;
		}
		return false;
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
}
?>