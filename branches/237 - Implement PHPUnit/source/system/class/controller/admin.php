<?php
class Controller_Admin{
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
		$perms = array("PV__100","PV__300","PV__400");
		
		RoutingEngine::getSmarty()->assign("page_perms", $perms);
		RoutingEngine::getSmarty()->assign("pages", Page::getAllPages());
	}
	public function stats(){
		RoutingEngine::getSmarty()->assign("stats",Stats::getRecentStats());
	}
	public function users(){
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
	public function update_module(){
		$module = new Module($_POST);
		if($module->update()){
			exit("success");
		}
		exit("DID NOT UPDATE");
	}
	public function modules(){
		$types = array("home","routes", "training", "community");
		
		RoutingEngine::getSmarty()->assign("module_types", $types);
		RoutingEngine::getSmarty()->assign("modules", Module::getAllModules());
	}
	public function action_new_pages(){
		foreach(RoutingEngine::$controllers as $c){
			$methods = get_class_methods("Controller_" . $c);
			//$c = str_replace("Controller_", "", $c);
			foreach($methods as $act){
				$page = new Page();
				$page->page_name = "{$c}/{$act}";
				if($c == "admin"){
					$page->perm_code = "PV__100";
				}
				else{
					$page->perm_code = "PV__300";
				}
				$page->createPage();
			}
		}
		Notification::add("Pages were added.");
		Page::redirect("/admin/pages");
	}
	public function action_new_modules(){
		$methods = get_class_methods("Controller_Module");
		foreach($methods as $act){
			if($act == "__construct") continue;
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