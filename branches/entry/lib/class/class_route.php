<?php
class Route{

	var $distance;
	var	$points;
	var	$comments;
	var	$name;
	var $id;
	var $start_lat;
	var $start_lng;
	var $date_creation;

	private $mysqli;

	function __construct($name = NULL){
		$this->mysqli = database::getDB();
		$this->name = $name;
	}

	/**
	 * Creates a new route with the given information
	 *
	 * @param User $user : user creating the route
	 * @param double $distance
	 * @param string $points
	 * @param string $comments
	 * @param string $name
	 * @return bool indicating the success
	 */
	function createNewRoute(User $user, $distance, $points, $comments, $name, $start_lat, $start_lng){
		$stmt = $this->mysqli->stmt_init();
		$stmt->prepare("INSERT INTO running.routes (r_uid ,r_distance ,r_points ,r_comments ,r_name ,r_creation, r_start_lat, r_start_lng) VALUES(?, ?,?,?,?, NOW(),?,?)") or die($stmt->error);
		$stmt->bind_param("idsssdd", $user->userID, $distance, $points, $comments, $name, $start_lat, $start_lng) or die($stmt->error);

		$stmt->execute() or die($stmt->error);
		$ins_id = $stmt->insert_id;
		$stmt->close();

		return $ins_id;
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
	
	function getRoundedDistance(){
		return round($this->distance, 2);
	}

	/**
	 * Returns the routes that were created by a specific user
	 *
	 * @param User $user : the user in question
	 * @return array of Route objects
	 */
	public static function getRoutesForUser(User $user){
		$mysqli = database::getDB();

		$stmt = $mysqli->prepare("SELECT r_name, r_id FROM routes WHERE r_uid=?") or die("error:".$stmt->error);
		$stmt->bind_param("i", $user->userID) or die("error:".$stmt->error);

		$stmt->execute() or die("error:".$stmt->error);
		$stmt->store_result();

		while ($row = $stmt->fetch_assoc()) {
			$route = new Route($row["r_name"]);
			$route->id = $row["r_id"];
			$output[] = $route;
		}

		$stmt->close();
		return $output;
	}

	/**
	 * Returns an array of the all the routes in the database.
	 * This function will soon be deprecated by more specific functions.
	 *
	 * @return array of Route objects
	 */
	public static function getAllRoutes(){
		$mysqli = database::getDB();
		$stmt = $mysqli->prepare("SELECT * FROM routes LIMIT 20");

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
		$mysqli = database::getDB();

		$stmt = $mysqli->prepare("SELECT * FROM routes WHERE r_id=?");
		$stmt->bind_param("i", $id);

		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$route = Route::fromFetchAssoc($row, true);

		$stmt->close();

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
	public static function fromFetchAssoc($row, $includePoints = false){
		$route = new Route();
			
		$route->distance = $row["r_distance"];
		$route->name = $row["r_name"];
		$route->start_lat = $row["r_start_lat"];
		$route->start_lng = $row["r_start_lng"];
		$route->id = $row["r_id"];
		$route->date_creation = $row["r_creation"];

		if($includePoints){
			$route->points = $row["r_points"];
		}
			
		return $route;
	}
}
?>