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
	
}

?>