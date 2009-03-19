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
}
?>