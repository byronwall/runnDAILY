<?php
class Controller_Module{
	/**
	 * @var SmartyExt
	 */
	private $_smarty;
	private $params;
	
	function __construct($params = null, $_smarty = null){
		if(is_null($smarty)){
			$smarty = new SmartyExt();
		}
		$this->_smarty = $smarty;
		$this->params = $params;
	}
	
	public function routes_recent(){
		$stmt = database::getDB()->prepare("
			SELECT *
			FROM routes
			WHERE r_uid=?
			ORDER BY r_creation	DESC
			LIMIT 10
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = new Route($row);
		}
		$stmt->close();
		
		$this->_smarty->assign("routes", $route_list);
		
		return Module::fromVariables(
			"routes_recent",
			"Recent Routes",
			3,
			$this->_smarty->fetch("modules/routes/route_list.tpl")
		);
		
	}
	public function routes_recently_run(){
		$stmt = database::getDB()->prepare("
			SELECT routes.*
			FROM routes
			JOIN training_times on t_rid = r_id
			WHERE r_uid=?
			ORDER BY t_date	DESC
			LIMIT 10
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = new Route($row);
		}
		$stmt->close();
		
		$this->_smarty->assign("routes", $route_list);
		
		return Module::fromVariables(
			"routes_recently_run",
			"Routes Recently Run",
			3,
			$this->_smarty->fetch("modules/routes/route_list.tpl")
		);
	}
	public function routes_parent_only(){
		$stmt = database::getDB()->prepare("
			SELECT routes.*
			FROM routes
			WHERE r_uid=? AND r_rid_parent IS NULL
			ORDER BY r_creation	DESC
			LIMIT 10
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = new Route($row);
		}
		$stmt->close();
		
		$this->_smarty->assign("routes", $route_list);
		
		return Module::fromVariables(
			"routes_parent_only",
			"Parent Routes Only",
			5,
			$this->_smarty->fetch("modules/routes/route_list.tpl")
		);
	}
	public function routes_friends(){
		$stmt = database::getDB()->prepare("
			SELECT routes.*
			FROM routes
			JOIN users_friends ON f_uid_2 = r_uid
			WHERE f_uid_1=?
			ORDER BY r_creation DESC
			LIMIT 10
		");
		$stmt->bind_param("i", User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$route_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$route_list[] = new Route($row);
		}
		$stmt->close();
		
		$this->_smarty->assign("routes", $route_list);
		
		return Module::fromVariables(
			"routes_friends",
			"Routes Created By Friends",
			null,
			$this->_smarty->fetch("modules/routes/route_list.tpl")
		);
	}
	public function routes_activity(){
		return $this->_activity(array(100,102));
	}
	private function _activity($aids, $title = "Activity"){
		$activity = Log::getActivityForUserByAid(User::$current_user->uid, $aids);
		
		$this->_smarty->assign("activity", $activity);
		
		return Module::fromVariables(
			"routes_friends",
			$title,
			null,
			$this->_smarty->fetch("modules/activity/list.tpl")
		);
	}
	public function training_calendar(){
		$cal = new Calendar(mktime(), CAL_MONTH);
		$training_items = TrainingLog::getItemsForUser(User::$current_user->uid, $cal->getFirstDayOnCalendar(), $cal->getLastDayOnCalendar());
		foreach($training_items as $item){
			$cal->addItemToDay($item->date, $item);
		}
		$this->_smarty->assign("calendar", $cal);
		
		return Module::fromVariables(
			"training_calendar",
			"Entries this month",
			5,
			$this->_smarty->fetch("modules/training/calendar.tpl")
		);
	}
	public function training_activity(){
		return $this->_activity(array(300,302), "Training activity");
	}
	public function community_friends(){
		$this->_smarty->assign("users", User::$current_user->getFriends());
		
		return Module::fromVariables(
			"community_friends",
			"your friends",
			null,
			$this->_smarty->fetch("modules/community/user_list.tpl")
		);
	}
	public function home_activity(){
		return $this->_activity(array(300,302,100,102,400), "All activity");
	}
	public function training_chart_distance(){
		$module = new Module();
		$module->content = $this->_smarty->fetch("modules/training/chart.tpl");
		$module->title = "Distances logged";
		
		return $module;
	}
}
?>