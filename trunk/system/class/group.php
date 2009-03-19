<?php
class Group extends Object{
	public $gid;
	public $name;
	public $desc;
	public $join;
	public $imgsrc;
	
	function __construct($arr = null, $arr_pre = "g_"){
		parent::__construct($arr, $arr_pre);
	}
	
	public static function fromGroupID($id){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM groups as g
			WHERE g.g_gid = ?
		");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$stmt->close();
		
		return new Group($row);
	}
	
	public function createGroup(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO groups
			SET
				g_name = ?,
				g_desc = ?,
				g_join = CURDATE()
		");
		$stmt->bind_param("ss", $this->name, $this->desc);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();
		
		if($rows == 1){
			$this->gid = $ins_id;
			return true;
		}
		return false;
	}
	
	public function updateImage($filename){
		$this->imgsrc = $filename;
		$stmt = Database::getDB()->prepare("
			UPDATE groups
			SET
				g_imgsrc = ?
			WHERE
				g_gid = ?
		");
		$stmt->bind_param("si", $this->imgsrc, $this->gid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			return true;
		}
	}
	
	public function getGroupList(){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM groups
		");
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$group_list = array();
		
		while($row = $stmt->fetch_assoc()){
			$group_list[] = new Group($row);
		}
		
		$stmt->close();
		
		return $group_list;
	}
}
?>