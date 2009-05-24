<?php
class Goal extends Object{
	public $id;
	public $uid;

	public $create;
	public $start;
	public $end;

	/*
	public $dist_tot;
	public $pace_avg;
	public $freq_tot;
	public $time_tot;
	*/
	
	public $metadata = array();

	public $name;
	public $desc;

	public $percent;

	function __construct($arr = null, $arr_pre = "go_"){
		parent::__construct($arr, $arr_pre);
		
		$this->create = strtotime($this->create);
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
	
	function getGoalsForUser($uid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM goals
			WHERE
				go_uid = ?
		");
		
		$stmt->bind_param("i", $uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$goal_list = array();

		while ($row = $stmt->fetch_assoc()) {
			$goal_list[] = new Goal($row);
		}

		$stmt->close();
		return $goal_list;
	}
	
	function getMetadataForGoal($id){
		
	}
	
	function getGoalById($id){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM goals
			WHERE
				go_id = ?
		");
		
		$stmt->bind_param("i", $id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$goal_list = array();

		$goal = new Goal($stmt->fetch_assoc());

		$stmt->close();
		
		return $goal;
	}
	
	function updatePercent(){
		
	}
}
?>