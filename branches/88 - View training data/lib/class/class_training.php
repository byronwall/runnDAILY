<?php
class TrainingLog{

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
			$training_items[] = TrainingLogItem::fromFetchAssoc($row);
		}

		return $training_items;
	}
}

class TrainingLogItem{
	var $date;
	var $time;
	var $distance;
	var $rid;
	var $uid;
	var $tid;
	var $route;

	public static function fromFetchAssoc($row, $shouldGetRoute = false){
		$item = new TrainingLogItem();

		$item->date = $row["t_date"];
		$item->distance = $row["t_distance"];
		$item->rid = $row["t_rid"];
		$item->tid = $row["t_tid"];
		$item->time = $row["t_time"];
		$item->uid = $row["t_uid"];

		if($shouldGetRoute){
			$item->route = Route::fromFetchAssoc($row);
		}
		return $item;
	}
	public static function getItem($tid){
		$stmt = database::getDB()->prepare("
		SELECT * FROM training_times as t
		LEFT JOIN routes as r ON r.r_id = t.t_rid
		WHERE t.t_tid = ?
		");
		$stmt->bind_param("i", $tid);
		$stmt->execute();
		$stmt->store_result();

		if($row = $stmt->fetch_assoc()){
			return TrainingLogItem::fromFetchAssoc($row, true);
		}
		else{
			return false;
		}
	}
}
?>