<?php
class training_controller{
	public function index(){
		$cal = new Calendar(mktime(), CAL_MONTH);
		
		$training_items = TrainingLog::getItemsForUser(User::$current_user->uid, $cal->getFirstDayOnCalendar(), $cal->getLastDayOnCalendar());
		
		foreach($training_items as $item){
			$cal->addItemToDay($item->date, $item);
		}
		
		Page::getSmarty()->assign("calendar", $cal);
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
		
		Page::getSmarty()->assign("item", $training_item);
		Page::getSmarty()->assign("calendar", $cal_week);		
	}
	public function create(){
		
	}
	public function browse(){
		$format = (isset($_GET["format"]))?$_GET["format"]:"html";
	
		//SQL query code
		$parser = new SqlParser(true, 5, 0);
		$parser->addCondition(new SqlRangeCondition("t_distance"));
		$parser->addCondition(new SqlRangeCondition("t_date", "FROM_UNIXTIME", "strtotime"));
		$parser->addCondition(new SqlRangeCondition("t_time", "", "TrainingLog::getSecondsFromFormat"));
		$parser->addCondition(new SqlLikeCondition("u_username"));
		$parser->addCondition(new SqlEqualCondition("u_uid"));
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
		
		Page::getSmarty()->assign("t_items", $t_items);
		Page::getSmarty()->assign("query", $parser->getQueryString(true, true));
		//END
		
		if($format == "ajax"){
			echo Page::getSmarty()->fetch("training/parts/item_list.tpl");
		}
	}
}
?>