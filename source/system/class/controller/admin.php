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
}
?>