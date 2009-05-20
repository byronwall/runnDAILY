<?php
class Goal extends Object{
	public $id;
	public $uid;

	public $create;
	public $start;
	public $end;

	public $dist_tot;
	public $pace_avg;
	public $freq_tot;
	public $time_tot;

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
		$ins_id = $stmt->insert_id;
		$stmt->close();

		if($rows == 1){
			$this->id = $ins_id;
			return true;
		}
		return false;
	}
	
	function updatePercent(){
		
	}
}
?>