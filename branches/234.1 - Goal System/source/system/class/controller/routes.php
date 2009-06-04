<?php
class Controller_Routes{
	public function index(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		$routes = Route::getRoutesForUserInArray(User::$current_user->uid, 50);
		$routes_js = json_encode_null($routes);
		
		RoutingEngine::getSmarty()->assign("routes", $routes);
		RoutingEngine::getSmarty()->assign("routes_js", $routes_js);
	}
	public function ajax_route_data(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		if(!isset($_GET["rid"])){
			RoutingEngine::returnAjax(false);
		}
		
		$route = Route::getPolyline($_GET["rid"]);
		
		//note that this already is JSON
		RoutingEngine::returnAjax($route, false);
	}
	public function view(){
		RoutingEngine::setPage("runnDAILY View Route", "PV__300");
		RoutingEngine::getInstance()->registerParams("rid");
		
		if(!isset($_GET["rid"])) Page::redirect("/routes");
		$rid = $_GET["rid"];
		$route = Route::fromRouteIdentifier($rid);
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
		
		$training_items = TrainingLog::getItemsForUserForRoute(User::$current_user->uid, $rid);
		
		RoutingEngine::getSmarty()->assign("t_types", $types);
		RoutingEngine::getSmarty()->assign("route_view", $route);
		RoutingEngine::getSmarty()->assign("training_items", $training_items);
	}
	public function browse(){
		//TODO: Implement this page if we want.
		RoutingEngine::setPage("runnDAILY Routes", "PV__100");
		
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
		RoutingEngine::setPage("runnDAILY Create Route", "PV__400");
		if(isset($_GET["rid"])){
			$route = Route::fromRouteIdentifier($_GET["rid"]);
			if($route->getTrainingCount() == 0){
				RoutingEngine::getSmarty()->assign("route_edit", $route);
				RoutingEngine::getSmarty()->assign("is_edit", true);
			}
			else{
				Notification::add("The route you tried to edit needs to be copied first.");
			}

			if(isset($_GET["mode"])){
				$isCopy = $_GET["mode"] == "copy";
				RoutingEngine::getSmarty()->assign("isCopy", $isCopy);
			}
		}
		RoutingEngine::getSmarty()->assign("body_id", "map_create");
	}
	public function action_create(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		$route = new Route($_POST);
		if($route->id){
			if($route->updateRoute()){
				Notification::add("Route - {$route->name} - was updated.");
				Page::redirect("/routes/view/{$route->id}/{$route->name}");
			}
		}
		else{		
			if($route->createRoute()){
				Notification::add("Route - {$route->name} - was created.");
				Page::redirect("/routes/view/{$route->id}/{$route->name}");
			}
		}
	}
	public function action_delete(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		$rid = $_POST["r_rid"];
		$uid = User::$current_user->uid;

		if(Route::deleteRouteSecure($rid, $uid)){
			Notification::add("Route was deleted.");
			Page::redirect("/routes/");
		}
		Page::redirect("/routes/view/{$rid}");
	}
	public function action_copy_edit(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		$route = new Route($_POST);
		if($route->copy()){
			Notification::add("Your route - {$route->name} - was copied.  You are now editing it.");
			Page::redirect("/routes/create?rid={$route->id}");
		}
		Notification::add("There was an error copying the route.  Try again.");
		Page::redirect("/routes/view/{$route->id}");
	}
	public function action_copy_view(){
		RoutingEngine::setPage("runnDAILY Routes", "PV__300");
		$route = new Route($_POST);
		if($route->copy()){
			Notification::add("Your route - {$route->name} - was copied.");
			Page::redirect("/routes/view/{$route->id}");
		}
		Notification::add("There was an error copying the route.  Try again.");
		Page::redirect("/routes/view/{$route->id}");
	}
	
}

?>