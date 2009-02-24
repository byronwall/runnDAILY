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
		
		RoutingEngine::getSmarty()->assign("message", $msgs);
	}
	public function index(){
		
	}
	public function pages(){
		RoutingEngine::getSmarty()->assign("pages", Page::getAllPages());
	}
	public function stats(){
		RoutingEngine::getSmarty()->assign("stats",Stats::getRecentStats());
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
		
		RoutingEngine::getSmarty()->assign("users", $users);
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
	public function modules(){
		RoutingEngine::getSmarty()->assign("modules", Module::getAllModules());
	}
	public function action_new_pages(){
		foreach(RoutingEngine::$controllers as $c){
			$methods = get_class_methods($c);
			$c = str_replace("_controller", "", $c);
			foreach($methods as $act){
				$page = new Page();
				$page->page_name = "{$c}/{$act}";
				if($c == "admin"){
					$page->min_permission = 100;
				}
				$page->createPage();
			}
		}
		Page::redirect("/admin/pages");
	}
	public function action_new_modules(){
		$methods = get_class_methods("module_controller");
		foreach($methods as $act){
			if($act = "__construct") continue;
			$module = new Module();
			$module->name = "{$act}";
			$module->createModule();
		}
		Page::redirect("/admin/modules");
	}
	public function action_hash_modules(){
		$modules = Module::getAllModules();		
		
		$contents = "<?php Module::\$hash = array(";
		
		$i = 0;
		foreach($modules as $module){
			if($i == 0) $i++;
			else $contents .= ",";
			$contents .= $module->code."=>\"".$module->name."\"";
		}
		
		$contents .= ") ?>";
		
		$filename = CLASS_ROOT."/hash_module.php";
		$handle = fopen($filename, "w");
		fwrite($handle, $contents);
		fclose($handle);
		
		Page::redirect("/admin/modules");		
	}
}
?>