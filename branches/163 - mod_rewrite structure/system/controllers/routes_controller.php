<?php
class routes_controller{
	public function index(){
		$recent_routes = Route::getRoutesForUser(User::$current_user->uid, 6);
		$recent_activity = Log::getRouteActivityForUser(User::$current_user->uid);
		$more_routes = Route::getRoutesForUser(User::$current_user->uid, 50);
		
		Page::getSmarty()->assign("recent_route_list", $recent_routes);
		Page::getSmarty()->assign("recent_activity_list", $recent_activity);
		Page::getSmarty()->assign("all_route_list", $more_routes);
	}
	public function view(){
		if(!isset($_GET["id"])){
			Page::redirect("/routes/");
		}
		$id = $_GET["id"];
		$route = Route::fromRouteIdentifier($id);
		Page::getSmarty()->assign("route_view", $route);
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
		
		Page::getSmarty()->assign("routes", $routes);
		Page::getSmarty()->assign("query", $parser->getQueryString(true, true));
		if($format == "ajax"){
			echo Page::getSmarty()->fetch("routes/parts/route_list.tpl");
		}
	}
	public function create(){
		if(isset($_GET["rid"])){
			$route = Route::fromRouteIdentifier($_GET["rid"]);
			Page::getSmarty()->assign("route_edit", $route);
			Page::getSmarty()->assign("is_edit", true);

			if(isset($_GET["mode"])){
				$isCopy = $_GET["mode"] == "copy";
				Page::getSmarty()->assign("isCopy", $isCopy);
			}
		}

		Page::getSmarty()->assign("body_id", "map_create");
	}
}
?>