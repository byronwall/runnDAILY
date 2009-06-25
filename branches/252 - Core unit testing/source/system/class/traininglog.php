<?php
class TrainingLog extends Object{
	const PREFIX = "t_";
	public $date;
	public $time;
	public $distance;
	public $rid;
	public $uid;
	public $tid;
	public $pace;
	public $type;
	public $type_name;
	public $type_desc;	
	//public $route;
	public $comment;

	function __construct($arr = null, $arr_pre = "t_"){
		parent::__construct($arr, $arr_pre);
		$this->date = strtotime($this->date);
		$this->time = TrainingLog::getSecondsFromFormat($this->time);
		//$this->route = new Route($arr);
	}

	static function sql(){
		return new SQL("training_times", __CLASS__, "t_tid");
	}
	/**
	 * Calculates the pace in MPH
	 * @return float
	 */
	public function getPace(){
		if($this->time == 0) return 0;
		return $this->distance * 3600 / $this->time;
	}
	
	/**
	 * Returns whether or not the user ID of the training log equals a given user ID.
	 * @param $uid: ID for comparison
	 * @return bool
	 */
	public function getIsOwnedBy($uid){
		return $this->uid == $uid;
	}

	/**
	 * Deletes the given trainging log from the database.
	 * It is marked as secure because it requires verification from the session data.
	 * @return bool
	 */
	public function deleteItemSecure(){
		$stmt = Database::getDB()->prepare("
			DELETE t FROM training_times as t, users as u
			WHERE
				t.t_tid = ? AND
				t.t_uid = u.u_uid AND
				u.u_uid = ?
		");
		$stmt->bind_param("ii", $this->tid, User::$current_user->uid );
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			Log::insertItem(User::$current_user->uid, 301, null, $this->rid, null, null);
			return true;
		}
		return false;
	}
	/**
	 * Updates the current item in the database
	 * @return bool
	 */
	public function updateItem(){
		$stmt = Database::getDB()->prepare("
			UPDATE training_times
			SET
				t_time = ?,
				t_distance = ?,
				t_date = FROM_UNIXTIME(?),
				t_pace = ?,
				t_type = ?,
				t_comment = ?
			WHERE t_tid = ?
		");
		$stmt->bind_param("ddsdisi", $this->time, $this->distance, $this->date, $this->getPace(), $this->type, $this->comment, $this->tid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			Log::insertItem(User::$current_user->uid, 302, null, $this->rid, $this->tid, null);
			return true;
		}
		return false;
	}
	/**
	 * Function is used to convert HH:MM:SS into seconds.
	 * @param $format: string containing HH:MM:SS or some derivative
	 * @return float
	 */
	public static function getSecondsFromFormat($format){
		$splits = split(":", $format);
		$time = 0;
		for($i = count($splits)-1; $i>=0;$i--){
			$time += $splits[$i] * pow(60, count($splits)- 1 - $i);
		}
		return $time;
	}

	/**
	 * Returns an array of training items for a given user within a given time frame.
	 * @param $uid
	 * @param $date_start
	 * @param $date_end
	 * @return array:TrainingLog
	 */
	public static function getItemsForUser($uid, $date_start = 0, $date_end = 0){
		if($date_end == 0) $date_end = mktime();
		$stmt = Database::getDB()->prepare(
		"SELECT *
		FROM training_times
		WHERE t_uid = ? AND t_date <= FROM_UNIXTIME(?) AND t_date >= FROM_UNIXTIME(?) ORDER BY t_date DESC"
		) or die("error with prepare");
		$stmt->bind_param("iii", $uid, $date_end, $date_start) or die($stmt->error);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result() or die($stmt->error);

		$training_items = array();

		while($row = $stmt->fetch_assoc()){
			$training_items[] = new TrainingLog($row);
		}
		
		$stmt->close();

		return $training_items;
	}
	public static function getItemsForUserForRoute($uid, $rid){
		$stmt = Database::getDB()->prepare("
			SELECT r_name, t_rid, t_tid, t_time, t_distance, t_pace, t_date, t_comment
			FROM training_times
			LEFT JOIN routes ON r_id = t_rid
			WHERE t_uid = ?
			AND t_rid = ?
			ORDER BY t_date DESC
		");
		$stmt->bind_param("ii", $uid, $rid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$training_items = array();

		while($row = $stmt->fetch_assoc()){
			$training_items[] = $row;
		}
		
		$stmt->close();

		return $training_items;
	}
	public static function getItemsForUserForGoalView($uid, $start, $end){
		$stmt = Database::getDB()->prepare("
			SELECT r_name, t_rid, t_tid, t_time, t_distance, t_pace, t_date, t_comment
			FROM training_times
			LEFT JOIN routes ON r_id = t_rid
			WHERE t_uid = ?
			AND t_date
				BETWEEN FROM_UNIXTIME( ? )
				AND FROM_UNIXTIME( ? )
			ORDER BY t_date DESC
		");
		$stmt->bind_param("iii", $uid, $start, $end);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$training_items = array();

		while($row = $stmt->fetch_assoc()){
			$training_items[] = $row;
		}
		
		$stmt->close();

		return $training_items;
	}
	public static function getItemsForUserForGoalPercent($uid, $start, $end){
		$stmt = Database::getDB()->prepare("
		SELECT COUNT( t_tid ) AS count, SUM( t_pace ) AS pace, SUM( t_distance ) AS dist, SUM( t_time ) AS time
		FROM training_times
		WHERE
			t_uid = ?
			AND t_date
				BETWEEN FROM_UNIXTIME( ? )
				AND FROM_UNIXTIME( ? )
		");
		$stmt->bind_param("iii", $uid, $start, $end);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$goal_data = $stmt->fetch_assoc();
		
		$stmt->close();
		
		return $goal_data;
	}
	public static function getSummaryOverall($uid){
		$stmt = Database::getDB()->prepare("
		SELECT COUNT( t_tid ) AS count, SUM( t_pace ) AS pace, SUM( t_distance ) AS dist, SUM( t_time ) AS time
		FROM training_times
		WHERE t_uid = ?
		");
		$stmt->bind_param("i", $uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$goal_data = $stmt->fetch_assoc();
		
		$stmt->close();
		
		return $goal_data;
	}
	public static function getItemsForUserPaged($uid, $count = 10, $page = 0){
		$limit_lower = $page * $count;
		$limit_upper = $page * $count + $count;

		$stmt = Database::getDB()->prepare("
			SELECT * 
			FROM training_times 
			WHERE t_uid=? 
			ORDER BY t_date DESC 
			LIMIT ?,?
		");
		$stmt->bind_param("iii", $uid, $limit_lower, $limit_upper);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$items = array();

		while ($row = $stmt->fetch_assoc()) {
			$items[] = new TrainingLog($row);
		}

		$stmt->close();
		return $items;
	}
	
	/**
	 * Function returns a training item with a given id
	 * @param $tid
	 * @return TrainingLog
	 */
	public static function getItem($tid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM training_times as t
			LEFT JOIN routes as r
			ON r.r_id = t.t_rid
			WHERE t.t_tid = ?
		");
		$stmt->bind_param("i", $tid);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		$stmt->close();

		return new TrainingLog($row);
	}
	
	public function createItem(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO training_times
			SET
				t_time = ?,
				t_distance = ?,
				t_pace = ?,
				t_date = FROM_UNIXTIME(?),
				t_rid = ?,
				t_uid = ?,
				t_type = ?,
				t_comment = ?
		");
		$stmt->bind_param("dddsiiis", $this->time, $this->distance, $this->getPace(), $this->date, $this->rid, User::$current_user->uid, $this->type, $this->comment);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();
		
		if($rows == 1){
			$this->tid = $ins_id;
			Log::insertItem(User::$current_user->uid, 300, null, $this->rid, $this->tid, null);
			return $ins_id;
		}
		return false;
	}
	
	public function getIndexItemsForUser($uid){
		$stmt = Database::getDB()->prepare("
			SELECT r_name, t_rid, t_tid, t_time, t_distance, t_pace, t_date, t_comment
			FROM training_times
			LEFT JOIN routes ON r_id = t_rid
			WHERE
				t_uid = ?
			ORDER BY
				t_date DESC,
				t_distance DESC
			LIMIT 50
		");
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$stmt->store_result();
		
		$items = array();
		
		while ($row = $stmt->fetch_assoc()) {
			$items[] = $row;
		}
		
		$stmt->close();
		
		return $items;
	}
	
	public function buildChartData($items){
		$data = array();
		if($items){
			$data["Distance_Data"] = array();
			$date["Pace_Data"] = array();
			$data["Distance_Max"] = 0;
			$data["Pace_Max"] = 0;
			$data["Date_Min"] = date("U", strtotime($items[count($items) - 1]["t_date"])) * 999.8;
			$data["Date_Max"] = date("U") * 1000;
			foreach($items as $item){
				$secs = strtotime($item["t_date"]);
				$data["Distance_Data"][] = array((date("U", $secs) - (4 * 60 * 60)) * 1000, $item["t_distance"] + 0);
				$data["Pace_Data"][] = array((date("U", $secs) - (4 * 60 * 60)) * 1000, $item["t_pace"] + 0);
				if ($item["t_distance"] > $data["Distance_Max"]){
					$data["Distance_Max"] = $item["t_distance"];
				}
				if ($item["t_pace"] > $data["Pace_Max"]){
					$data["Pace_Max"] = $item["t_pace"];
				}
			}
			$data["Distance_Max"] = ceil($data["Distance_Max"]);
			$data["Pace_Max"] = ceil($data["Pace_Max"]);
		}else{
			$data["Distance_Data"] = array(date("U") * 1000, 0);
			$data["Pace_Data"] = array(date("U") * 1000, 0);
			$data["Distance_Max"] = 1;
			$data["Pace_Max"] = 1;
			$data["Date_Min"] = (date("U") * 1000) - (7 * 24 * 60 * 60 * 1000);
			$data["Date_Max"] = date("U") * 1000;
		}
		return json_encode($data);
	}
}
?>