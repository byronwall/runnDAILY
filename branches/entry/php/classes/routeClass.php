<?php
class Route{

	var $distance;
	var	$points;
	var	$comments;
	var	$name;
	var $id;

	private $mysqli;

	function __construct($name = NULL){
		global $SETTINGS;
		$this->mysqli = $SETTINGS["dbconn"];
		$this->name = $name;
	}

	/**
	 * This function is used to add a new route to the database.
	 *
	 * @param User $user
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
	public static function getRoutesForUser(User $user){
		global $SETTINGS;
		$mysqli = $SETTINGS["dbconn"];

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
	public static function getAllRoutes(){
		global $SETTINGS;
		$mysqli = $SETTINGS["dbconn"];

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

	public static function fromRouteIdentifier($id){
		global $SETTINGS;
		$mysqli = $SETTINGS["dbconn"];

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