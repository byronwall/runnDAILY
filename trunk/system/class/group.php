<?php
class Group extends Object{
	public $gid;
	public $name;
	public $desc;
	public $join;
	public $imgsrc;
	public $private;
	
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
				g_join = CURDATE(),
				g_private = ?
		");
		$stmt->bind_param("ssi", $this->name, $this->desc, $this->private);
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
	
	public function createAnnouncement(){
		$gid = $_POST["gid"];
		$anoun = $_POST["gm_anoun"];
		if(Group::insertMetadata($gid, "anoun", $anoun) && Group::insertMetadata($gid, "anoun_date", date("Y-m-d"))){
			return nl2br($anoun);
		}
	}
	
	public function insertMetadata($gid, $gm_key, $gm_value){
		$stmt = Database::getDB()->prepare("
			REPLACE INTO groups_metadata
			SET
				gm_gid = ?,
				gm_key = ?,
				gm_value = ?
		");
		$stmt->bind_param("iss", $gid, $gm_key, $gm_value);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows);
	}
	
	public function getAnnouncement($gid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM groups_metadata
			WHERE
				gm_gid = ? AND
				gm_key = 'anoun'
		");
		$stmt->bind_param("i", $gid);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->affected_rows > 0){
			$row = $stmt->fetch_assoc();
			$anoun = $row["gm_value"];
		}else{
			$anoun = "There are currently no announcements.";
		}
		
		$stmt->close();
		
		return $anoun;
	}
	
	public function joinGroup($gid){
		$stmt = Database::getDB()->prepare("
			INSERT INTO groups_members
			SET
				gmem_gid = ?,
				gmem_uid = ?,
				gmem_join = NOW()
		");
		$stmt->bind_param("ii", $gid, User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows == 1);
	}
	
	public function userIsMember($gid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM groups_members
			WHERE
				gmem_gid = ? AND
				gmem_uid = ?
		");
		$stmt->bind_param("ii", $gid, User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows;
	}
	
	public function userCanEdit($gid){
		return RoutingEngine::getInstance()->requirePermission("GP__100", $gid);
	}
	
	public function leaveGroup($gid){
		$stmt = Database::getDB()->prepare("
			DELETE
			FROM groups_members
			WHERE
				gmem_gid = ? AND
				gmem_uid = ?
		");
		$stmt->bind_param("ii", $gid, User::$current_user->uid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();

		return ($rows > 0);
	}
	
	public function getMembers($gid){
		$stmt = Database::getDB()->prepare("
			SELECT
				gmem_uid AS uid,
				users.u_username AS username
			FROM groups_members
			LEFT JOIN users ON users.u_uid = gmem_uid
			WHERE
				gmem_gid = ?
		");
		$stmt->bind_param("i", $gid);
		$stmt->execute();
		$stmt->store_result();
		
		$member_list = array();
		while($row = $stmt->fetch_assoc()){
			$member_list[] = $row;
		}
		$stmt->close();
		
		return $member_list;
	}
}
?>