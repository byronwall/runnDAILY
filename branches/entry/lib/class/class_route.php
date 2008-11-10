<?php
class Route{

	var $distance;
	var	$points;
	var	$comments;
	var	$name;
	var $id;

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
	function createNewRoute(User $user, $distance, $points, $comments, $name){
		$stmt = $this->mysqli->stmt_init();
		$stmt->prepare("INSERT INTO running.routes (uid ,distance ,points ,comments ,name ,creation) VALUES(?, ?,?,?,?, NOW())") or die($stmt->error);
		$stmt->bind_param("idsss", $user->userID, $distance, $points, $comments, $name) or die($stmt->error);

		$stmt->execute() or die($stmt->error);

		if($stmt->affected_rows ==1){
			echo "created successfully";
		}
		else{
			$stmt->close();
			die("there was an error creating the route.");
		}
		$stmt->close();

		return true;
	}
	
	/**
	 * Returns the routes that were created by a specific user
	 *
	 * @param User $user : the user in question
	 * @return array of Route objects
	 */
	public static function getRoutesForUser(User $user){
		$mysqli = database::getDB();

		$stmt = $mysqli->prepare("SELECT routes.name, id FROM routes WHERE uid=?") or die("error:".$stmt->error);
		$stmt->bind_param("i", $user->userID) or die("error:".$stmt->error);

		$stmt->execute() or die("error:".$stmt->error);
		$stmt->store_result();

		while ($row = $stmt->fetch_assoc()) {
			$route = new Route($row["name"]);
			$route->id = $row["id"];
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

		$stmt = $mysqli->prepare("SELECT name, id, distance FROM routes");

		$stmt->execute();
		$stmt->store_result();

		$routes = array();

		while($row = $stmt->fetch_assoc()){
			$route = new Route($row["name"]);
			$route->id = $row["id"];
			$route->distance = $row["distance"];

			$routes[] = $route;
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

		$stmt = $mysqli->prepare("SELECT points, name, distance FROM routes WHERE id=?");
		$stmt->bind_param("i", $id);

		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();

		$route = new Route($row["name"]);
		$route->points = $row["points"];


		$stmt->close();

		return $route;
	}
}
?>