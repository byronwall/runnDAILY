<?php
class TrainingLog{
	var $date;
	var $time;
	var $distance;
	var $rid;
	var $uid;
	var $tid;
	var $pace;

	var $route;

	/**
	 * Calculates the pace in MPH
	 * @return float
	 */
	public function getPace(){
		return $this->distance * 3600 / $this->time;
	}
	
	/**
	 * Function takes an array and attempts to convert it into a trainging object.
	 * @param $row: the array with data
	 * @param $shouldGetRoute: whether or not to search for route data
	 * @return TrainingLog
	 */
	public static function fromFetchAssoc($row, $shouldGetRoute = false){
		$item = new TrainingLog();

		$item->date = isset($row["t_date"])?$row["t_date"] : null;
		$item->distance = isset($row["t_distance"])? $row["t_distance"]: null;
		$item->rid = isset($row["t_rid"])?$row["t_rid"] : null;
		$item->tid = isset($row["t_tid"])?$row["t_tid"] : null;
		$item->time = isset($row["t_time"])? $item->getSecondsFromFormat($row["t_time"]) : null ;
		$item->uid = isset($row["t_uid"])?$row["t_uid"] : null;
		$item->pace = isset($row["t_pace"])?$row["t_pace"] : null;

		if($shouldGetRoute){
			$item->route = Route::fromFetchAssoc($row);
		}
		return $item;
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
		$stmt = database::getDB()->prepare("
			DELETE t FROM training_times as t, users as u
			WHERE
				t.t_tid = ? AND
				t.t_uid = u.u_uid AND
				u.u_uid = ?
		");
		$stmt->bind_param("ii", $this->tid, $_SESSION["userData"]->userID );
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			return true;
		}
		return false;
	}
	/**
	 * Updates the current item in the database
	 * @return bool
	 */
	public function updateItem(){
		$stmt = database::getDB()->prepare("
			UPDATE training_times
			SET
				t_time = ?,
				t_distance = ?,
				t_date = ?,
				t_pace = ?
			WHERE t_tid = ?
		");
		$stmt->bind_param("ddsdi", $this->time, $this->distance, $this->date, $this->getPace(), $this->tid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			return true;
		}
		return false;
	}
	/**
	 * Function is used to convert HH:MM:SS into seconds.
	 * @param $format: string containing HH:MM:SS or some derivative
	 * @return float
	 */
	public function getSecondsFromFormat($format){
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
		$stmt = database::getDB()->prepare(
		"SELECT *
		FROM training_times
		WHERE t_uid = ? AND t_date <= FROM_UNIXTIME(?) AND t_date >= FROM_UNIXTIME(?)"
		) or die("error with prepare");
		$stmt->bind_param("iii", $uid, $date_end, $date_start) or die($stmt->error);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result() or die($stmt->error);

		$training_items = array();

		while($row = $stmt->fetch_assoc()){
			$training_items[] = TrainingLog::fromFetchAssoc($row);
		}

		return $training_items;
	}
	/**
	 * Function returns a training item with a given id
	 * @param $tid
	 * @return TrainingLog
	 */
	public static function getItem($tid){
		$stmt = database::getDB()->prepare("
		SELECT * FROM training_times as t
		LEFT JOIN routes as r ON r.r_id = t.t_rid
		WHERE t.t_tid = ?
		");
		$stmt->bind_param("i", $tid);
		$stmt->execute();
		$stmt->store_result();

		$row = $stmt->fetch_assoc();
		$stmt->close();

		return TrainingLog::fromFetchAssoc($row, true);
	}
}
?>