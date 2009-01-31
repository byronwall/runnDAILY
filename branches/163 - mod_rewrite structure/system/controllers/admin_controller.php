<?php
class admin_controller{
	public function feedback(){
		$result = Database::getDB()->query("
			SELECT * FROM messages as m
			LEFT JOIN users ON u_uid = m_uid_from
			WHERE m.m_uid_to = 0
		");
		$msgs = array();
		
		while($row = $result->fetch_assoc()){
			$msgs[] = new Message($row);
		}
		
		$result->close();
		
		Page::getSmarty()->assign("message", $msgs);
	}
	public function index(){
		
	}
	public function pages(){
		Page::getSmarty()->assign("pages", Page::getAllPages());
	}
	public function stats(){
		Page::getSmarty()->assign("stats",Stats::getRecentStats());
	}
	public function users(){
		$parser = new SqlParser(true, 10, 0);
		$parser->addCondition(new SqlRangeCondition("u_date_access", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new SqlLikeCondition("u_username"));
		$parser->addCondition(new SqlLikeCondition("u_email"));
		$parser->addCondition(new SqlEqualCondition("u_uid"));
		$parser->addCondition(new SqlRangeCondition("u_type"));
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
		
		Page::getSmarty()->assign("users", $users);
	}
	public function update_stats(){
		if(Stats::insertStats()){
			exit("success");
		}
		echo "DID NOT WORK";		
	}
	public function update_page(){
		$p_page = new Page($_POST, "p_");
		if($p_page->updatePage()){
			exit("success");
		}
		exit("DID NOT UPDATE");
	}
}
?>