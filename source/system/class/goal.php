<?php
class Goal extends Object{
	public $id;
	public $uid;

	public $start;
	public $end;

	public $metadata = array();

	public $name;
	public $desc;

	public $percent;

	function __construct($arr = null, $arr_pre = "go_"){
		parent::__construct($arr, $arr_pre);
		
		$this->start = strtotime($this->start);
		$this->end = strtotime($this->end);
	}

	function createGoal(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO goals
			SET
				go_uid = ?,
				go_create = CURDATE(),
				go_start = FROM_UNIXTIME(?),
				go_end = FROM_UNIXTIME(?),
				go_name = ?,
				go_desc = ?
		");
		$stmt->bind_param("issss",
							User::$current_user->uid,
							$this->start,
							$this->end,
							$this->name,
							$this->desc
							);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		
		if($stmt->error){
			$stmt->close();
			return false;
		}
		
		$ins_id = $stmt->insert_id;
		$stmt->close();
		
		if($ins_id){
			$this->id = $ins_id;
			foreach($this->metadata as $key=>$value){
				$this->saveMetadata($key);
			}
		}
		
		return true;
	}
	
	public static function deleteGoal($id){
		$stmt = Database::getDB()->prepare("
			DELETE FROM goals
			WHERE
				go_id = ?
				AND
				go_uid = ?
		");
		$stmt->bind_param("ii", $id, User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
	}
	
	function saveMetadata($key){
		if(!isset($key) || !isset($this->metadata[$key])) return false;
		
		$stmt = Database::getDB()->prepare("
			REPLACE INTO goals_metadata
			SET
				gom_goid = ?,
				gom_key = ?,
				gom_value = ?
		");
		$stmt->bind_param("iss", $this->id, $key, $this->metadata[$key]);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows == 1;
	}
	
	public static function getGoalsForUser($uid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM goals
			WHERE
				go_uid = ?
			ORDER BY go_end ASC
		");
		
		$stmt->bind_param("i", $uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$goal_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$goal = new Goal($row);
			$goal->metadata = $goal->getMetadataForGoal($goal->id);
			if(strtotime("today") <= $goal->end){
				$goal_list['active'][] = $goal;
			}else{
				$goal_list['past'][] = $goal;
			}
		}

		$stmt->close();
		
		return $goal_list;
	}
	
	function getMetadataForGoal(){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM goals_metadata
			LEFT JOIN goals_metadata_keys
			USING ( gom_key )
			WHERE
				gom_goid = ?
		");
		$stmt->bind_param("i", $this->id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$metadata = array();
		
		while ($row = $stmt->fetch_assoc()){
			$index = $row['gom_key'];
			$metadata[$index] = array("value" => $row['gom_value'], "desc" => $row['gom_key_desc']);
		}
		$stmt->close();
		
		return $metadata;
	}
	
	public static function getGoalById($goid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM goals
			WHERE
				go_id = ?
		");
		
		$stmt->bind_param("i", $goid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$goal = new Goal($stmt->fetch_assoc());

		$stmt->close();
		
		$goal->metadata = $goal->getMetadataForGoal();
		
		//$goal->updatePercent();
		
		return $goal;
	}
	
	function updatePercent(){
		$goal_data = TrainingLog::getItemsForUserForGoalPercent(User::$current_user->uid, $this->start, $this->end);
		$percents = array();
		$percent = 1;
		
		if(isset($this->metadata['dist_tot'])){
			$percents['dist'] = $goal_data['dist'] / $this->metadata['dist_tot']['value'];
		}
		
		if(isset($this->metadata['pace_avg'])){
			$percents['pace'] = ($goal_data['pace'] / $goal_data['count']) / $this->metadata['pace_avg']['value'];
		}
		
		if(isset($this->metadata['time_tot'])){
			$percents['time'] = ($goal_data['time'] / 60.0) / $this->metadata['time_tot']['value'];
		}
		
		var_dump($percents);
		
		foreach($percents as $item){
			if($item <= 1){
				$percent = $percent * $item;
			}
		}
		
		$this->percent = $percent * 100;
		
		$stmt = Database::getDB()->prepare("
			UPDATE goals
			SET go_percent = ?
			WHERE go_id = ?
		");
		$stmt->bind_param("di", $this->percent, $this->id);
		$stmt->execute() or die($stmt->error);
		$stmt->close();
	}
	
	public static function getGoalIdsForUserInRange($uid, $date){
		$stmt = Database::getDB()->prepare("
			SELECT go_id
			FROM goals
			WHERE
				go_uid = ?
				AND go_start <= FROM_UNIXTIME( ? )
				AND go_end >= FROM_UNIXTIME( ? )
		");
		
		$stmt->bind_param("iii", $uid, $date, $date);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		
		$goal_ids = array();
		
		while ($row = $stmt->fetch_assoc()) {
			$goal_ids[] = $row;
		}

		$stmt->close();
		
		return $goal_ids;
	}
	
	public static function updatePercentForList($goal_list){
		foreach($goal_list as $item){
			$goal = Goal::getGoalById($item['go_id']);
			$goal->updatePercent();
		}
	}
	
	function buildGoalDataUsingTrainingItems($training_items){
		$goal_data = array("dist_tot" => 0, "pace_avg" => 0, "time_tot" => 0);
		
		foreach($training_items as $item){
			$goal_data['dist_tot'] += $item['t_distance'];
			$goal_data['pace_avg'] += $item['t_pace'];
			$goal_data['time_tot'] += $item['t_time'];
		}
		
		//account for division by zero.
		$count = count($training_items);
		if($count) $goal_data['pace_avg'] /= count($training_items);
		else $goal_data['pace_avg'] = 0;
		
		$goal_data['time_tot'] /= 60;
		
		foreach($goal_data as $key => $value){
			foreach($this->metadata as $meta_key => $meta_value){
				if($key == $meta_key){
					$this->metadata[$key]['current'] = $value;
				}
			}
		}
	}
}
?>