<?php
class TrainingLog extends Object{
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
	public $route;

	function __construct($arr = null, $arr_pre = "t_"){
		parent::__construct($arr, $arr_pre);
		$this->date = strtotime($this->date);
		$this->time = TrainingLog::getSecondsFromFormat($this->time);
		$this->route = new Route($arr);
	}	
	/**
	 * Calculates the pace in MPH
	 * @return float
	 */
	public function getPace(){
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
			Log::insertItem(User::$current_user->uid, 301, null, $this->rid, $this->tid, null);
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
				t_pace = ?
			WHERE t_tid = ?
		");
		$stmt->bind_param("ddsdi", $this->time, $this->distance, $this->date, $this->getPace(), $this->tid);
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
	 * @return array:TrainingLogg
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

		return $training_items;
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
		SELECT * FROM training_times as t
		LEFT JOIN routes as r ON r.r_id = t.t_rid
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
				t_type = ?
		");
		$stmt->bind_param("dddsiii", $this->time, $this->distance, $this->getPace(), $this->date, $this->rid, User::$current_user->uid, $this->type);
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
	
	public function getItemsByMonth($year_month)
	{
		$stmt = Database::getDB()->prepare("
			SELECT * FROM training_times
			WHERE t_date >= ?
				AND t_date <= LAST_DAY(?)
				AND t_uid = ?
		");
		$mysql_date = date("Y-m-d", strtotime($year_month."-01"));
		$stmt->bind_param("ssi", $mysql_date, $mysql_date, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$items = array();

		while ($row = $stmt->fetch_assoc()) {
			$items[] = new TrainingLog($row);
		}

		$stmt->close();
		
		return $items;
	}
}
?>