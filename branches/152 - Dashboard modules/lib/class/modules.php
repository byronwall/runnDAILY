<?php
class Module{
	public $content;
	public $id;
	public $title;
	public $size;
	
	public function __construct($id = null, $title = null, $size = null, $content = null){
		$this->id = $id;
		$this->content = $content;
		$this->title = $title;
		$this->size = $size;
	}
	
	public static function draw_routes_recent($params = null){
		$stmt = database::getDB()->prepare("
			SELECT * 
			FROM routes 
			WHERE r_uid=? 
			ORDER BY r_creation	DESC 
			LIMIT 10
		");
		$stmt->bind_param("i", $_SESSION["userData"]->userID);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = classFromArray("Route", $row, "r_");
			//$route_list[] = Route::fromFetchAssoc($row, true);
		}
		$stmt->close();
		
		$smarty = new Smarty_Ext();
		$smarty->assign("routes", $route_list);
		
		return new Module("routes_recent", "Recent Routes", 3, $smarty->fetch("modules/routes_recent.tpl"));
	}
	public static function draw_routes_recently_run($params = null){
		$stmt = database::getDB()->prepare("
			SELECT routes.* 
			FROM routes 
			JOIN training_times on t_rid = r_id
			WHERE r_uid=? 
			ORDER BY t_date	DESC 
			LIMIT 10
		");
		$stmt->bind_param("i", $_SESSION["userData"]->userID);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = Route::fromFetchAssoc($row, true);
		}
		$stmt->close();
		
		$smarty = new Smarty_Ext();
		$smarty->assign("routes", $route_list);
		
		return new Module("routes_recently_run", "Routes Recently Run", 3, $smarty->fetch("modules/routes_recent.tpl"));			
	}
	public static function draw_routes_parent_only($params = null){
		$stmt = database::getDB()->prepare("
			SELECT routes.* 
			FROM routes 
			WHERE r_uid=? AND r_rid_parent IS NULL 
			ORDER BY r_creation	DESC 
			LIMIT 10
		");
		$stmt->bind_param("i", $_SESSION["userData"]->userID);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = Route::fromFetchAssoc($row, true);
		}
		$stmt->close();
		
		$smarty = new Smarty_Ext();
		$smarty->assign("routes", $route_list);
		
		return new Module("routes_parent_only", "Parent Routes Only", 3, $smarty->fetch("modules/routes_recent.tpl"));			
	}
	public static function draw_routes_friends($params = null){
		$stmt = database::getDB()->prepare("
			SELECT routes.* 
			FROM routes 
			JOIN users_friends ON f_uid_2 = r_uid
			WHERE f_uid_1=? 
			ORDER BY r_creation DESC 
			LIMIT 10
		");
		$stmt->bind_param("i", $_SESSION["userData"]->userID);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = Route::fromFetchAssoc($row, true);
		}
		$stmt->close();
		
		$smarty = new Smarty_Ext();
		$smarty->assign("routes", $route_list);
		
		return new Module("routes_friends", "Routes Created By Friends", 3, $smarty->fetch("modules/routes_recent.tpl"));
	}
}
?>