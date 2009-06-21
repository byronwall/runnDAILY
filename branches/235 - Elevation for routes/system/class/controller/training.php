<?php
class Controller_Training{
	public function index(){
		$index_items = TrainingLog::getIndexItemsForUser(User::$current_user->uid);
		$json_data = TrainingLog::buildChartData($index_items);
		RoutingEngine::getSmarty()->assign("training_index_items", $index_items);
		RoutingEngine::getSmarty()->assign("JSON_Chart_Data", $json_data);
	}
	public function view(){
		if(!isset($_GET["tid"])){
			Page::redirect("/training/");
		}
		
		$tid = $_GET["tid"];
		
		$training_item = TrainingLog::getItem($tid);
		$cal_week = new Calendar($training_item->date, CAL_WEEK);
		$training_items = TrainingLog::getItemsForUser($training_item->uid, $cal_week->getFirstDayOnCalendar(), $cal_week->getLastDayOnCalendar());
		foreach($training_items as $item){
			$cal_week->addItemToDay($item->date, $item);
		}
		
		RoutingEngine::getSmarty()->assign("item", $training_item);
		RoutingEngine::getSmarty()->assign("calendar", $cal_week);
	}
	public function browse(){
		$format = (isset($_GET["format"]))?$_GET["format"]:"html";
	
		$parser = new Sql_Parser(true, 5, 0);
		$parser->addCondition(new Sql_RangeCondition("t_distance"));
		$parser->addCondition(new Sql_RangeCondition("t_date", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new Sql_RangeCondition("t_time", "", "TrainingLog::getSecondsFromFormat"));
		$parser->addCondition(new Sql_LikeCondition("u_username"));
		$parser->addCondition(new Sql_EqualCondition("u_uid"));
		$parser->setData($_GET);
		
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM training_times
			JOIN users ON u_uid = t_uid
			WHERE
				{$parser->getSQL()}
		");
		
		$parser->bindParamToStmt($stmt);
		$stmt->execute();
		$stmt->store_result();
		
		$t_items = array();
		while($row = $stmt->fetch_assoc()){
			$t_items[] = new TrainingLog($row);
		}
		
		RoutingEngine::getSmarty()->assign("t_items", $t_items);
		RoutingEngine::getSmarty()->assign("query", $parser->getQueryString(true, true));
		
		if($format == "ajax"){
			exit(RoutingEngine::getSmarty()->fetch("training/parts/item_list.tpl"));
		}
	}
	public function action_edit(){
		$t_item = new TrainingLog($_POST);
		if($t_item->updateItem() ){
			Page::redirect("/training/");
		}
		Page::redirect("/training/");
	}
	public function action_delete(){
		$t_item = new TrainingLog($_POST);
		if($t_item->deleteItemSecure()){
			Page::redirect("/training/");
		}
		Page::redirect("/training/create?tid={$t_item->tid}");
	}
	public function action_save(){
		$t_item = new TrainingLog($_POST);
		if(array_safe($_POST, "t_rid") == "") $t_item->rid = null;
		if($t_item->createItem()){
			Page::redirect("/training/");
		}
		Page::redirect("/training/");
	}
	
	public function create(){
		$stmt = Database::getDB()->prepare("
			SELECT r_name, r_distance, r_id
			FROM routes
			WHERE
				r_uid = ?
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$routes = array();
		while($row = $stmt->fetch_assoc()){
			$route = new Route($row);
			$routes[$route->id] = $route;
		}
		$stmt->close();
		//get training types
		
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
		RoutingEngine::getSmarty()->assign("routes_json", json_encode_null($routes));
		RoutingEngine::getSmarty()->assign("routes", $routes);
	}
	function edit(){
		if(!isset($_GET["tid"])){
			Notification::add("That training entry does not exist.");
			Page::redirect("/training");
		}
		$tid = $_GET["tid"];
		
		$training = TrainingLog::getItem($tid);
		if($training->uid != User::$current_user->uid){
			Notification::add("You are not allowed to edit that entry");
			Page::redirect("/training");
		}
		
		$stmt = Database::getDB()->prepare("
			SELECT t_type_id, t_type_name
			FROM training_types
		");
		$stmt->execute();
		$stmt->store_result();
		$types = array();
		while($row = $stmt->fetch_assoc()){
			$types[$row["t_type_id"]] = $row["t_type_name"];
		}
		$stmt->close();
		
		RoutingEngine::getSmarty()->assign("t_types", $types);		
		RoutingEngine::getSmarty()->assign("t_item", $training);
		RoutingEngine::getSmarty()->display("training/edit.tpl");
		exit;
	}
}
?>