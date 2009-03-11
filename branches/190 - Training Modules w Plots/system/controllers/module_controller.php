<?php
class module_controller{
	/**
	 * @var Smarty_Ext
	 */
	private $_smarty;
	private $params;
	
	function __construct($params = null, $_smarty = null){
		if(is_null($smarty)){
			$smarty = new Smarty_Ext();
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
			"Recently Created Routes",
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
		return $this->_activity(array(100,102), "Route Activity");
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
			"Training Calendar",
			5,
			$this->_smarty->fetch("modules/training/calendar.tpl")
		);
	}
	public function training_activity(){
		return $this->_activity(array(300,302), "Training Activity");
	}
	public function community_friends(){
		$this->_smarty->assign("users", User::$current_user->getFriends());
		
		return Module::fromVariables(
			"community_friends",
			"Friends",
			null,
			$this->_smarty->fetch("modules/community/user_list.tpl")
		);
	}
	public function home_activity(){
		return $this->_activity(array(300,302,100, 102), "All Activity");
	}
	public function training_chart_distance(){		
		$training_item_list = TrainingLog::getItemsByMonth(date("Y-m"));
		
		foreach ($training_item_list as $list_item)
		{
			$day_num = (int) date("j", $list_item->date);
			$distance_data[$day_num] += $list_item->distance;
			$pace_data[$day_num] += $list_item->pace;
		}
		
		$this->_smarty->assign("training_plot_distance_data", $distance_data);
		$this->_smarty->assign("training_plot_pace_data", $pace_data);
		
		$module = new Module();
		$module->content = $this->_smarty->fetch("modules/training/chart.tpl");
		$module->title = "Distance and Pace Chart";
		
		return $module;
	}
}
?>