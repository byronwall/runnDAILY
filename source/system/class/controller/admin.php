<?php
class Controller_Admin{
	public function feedback(){
		RoutingEngine::setPage("runnDAILY Admin", "PV__100");
		$message_list = Message::getMessagesByType(2);		
		RoutingEngine::getSmarty()->assign("message_list", $message_list);
	}
	public function index(){
		RoutingEngine::setPage("runnDAILY Admin", "PV__100");
	}
	public function users(){
		RoutingEngine::setPage("runnDAILY Admin", "PV__100");
		$parser = new Sql_Parser(true, 10, 0);
		$parser->addCondition(new Sql_RangeCondition("u_date_access", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new Sql_LikeCondition("u_username"));
		$parser->addCondition(new Sql_LikeCondition("u_email"));
		$parser->addCondition(new Sql_EqualCondition("u_uid"));
		$parser->addCondition(new Sql_RangeCondition("u_type"));
		$parser->setData($_GET);
		
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM users
			WHERE
				{$parser->getSQL()}
		");
		
		$parser->bindParamToStmt($stmt);
		$stmt->execute();
		$stmt->store_result();
		
		$users = array();
		while($row = $stmt->fetch_assoc()){
			$users[] = new User($row);
		}
		
		RoutingEngine::getSmarty()->assign("users", $users);
	}
	public function elevation(){
		RoutingEngine::setPage("runnDAILY Elevation Admin", "PV__100");
		
		$query = Database::getDB()->query("
			SELECT * FROM elevation_regions
		");
		$elev = array();
		while($row = $query->fetch_assoc()){
			$elev[$row["id"]] = $row;
		}
		RoutingEngine::getSmarty()->assign("elevation", json_encode($elev));
	}
	public function action_elevation_pack(){
		if($_FILES["large_hdr"]){
			move_uploaded_file($_FILES["large_hdr"]["tmp_name"], SYSTEM_ROOT . "/elev.hdr");
		}
		if($_FILES["large_flt"]){
			echo move_uploaded_file($_FILES["large_flt"]["tmp_name"], SYSTEM_ROOT . "/elev.flt");
		}
		Elevation::repackFile(SYSTEM_ROOT . "/elev", 6);
		unlink(SYSTEM_ROOT . "/elev.hdr");
		unlink(SYSTEM_ROOT . "/elev.flt");
		
		Notification::add("Your packed elevation file has been created.");
		Page::redirect("/admin/elevation");
		
	}
	public function action_elevation_add_packed(){
		if($_FILES["packed_hdr"]){
			move_uploaded_file($_FILES["packed_hdr"]["tmp_name"], SYSTEM_ROOT . "/elev_packed.hdr");
		}
		if($_FILES["packed_flt"]){
			move_uploaded_file($_FILES["packed_flt"]["tmp_name"], SYSTEM_ROOT . "/elev_packed.flt");
		}
		$region = array_safe($_POST, "region_name", "Elevation data");
		Elevation::addElevationToDatabase(SYSTEM_ROOT . "/elev_packed", 1, $region);
		unlink(SYSTEM_ROOT . "/elev_packed.hdr");
		unlink(SYSTEM_ROOT . "/elev_packed.flt");
		
		Notification::add("Elevation data has been added");
		Page::redirect("/admin/elevation");
	}
}
?>