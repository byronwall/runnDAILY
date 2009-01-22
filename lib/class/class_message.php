<?php
class Message extends Object{

	public $mid;
	public $uid_to;
	public $uid_from;
	public $msg;
	public $new;
	public $date;

	public $user;

	function __construct($arr = null, $arr_pre = "m_"){
		parent::__construct($arr, $arr_pre);
		
		$this->user = new User($arr);
	}
	
	function createOrUpdateMessage(){
		if(!$this->mid){
			return $this->createMessage();
		}
		else{
			return $this->updateMessage();
		}
	}
	private function createMessage(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO messages(m_uid_to, m_uid_from, m_msg, m_date)
			VALUES(?,?,?,NOW())
		");
		$stmt->bind_param("iis", $this->uid_to, $this->uid_from, $this->msg) or die($stmt->error);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			$stmt = Database::getDB()->prepare("
				UPDATE users SET u_msg_new = u_msg_new + 1 WHERE u_uid = ?
			");
			$stmt->bind_param("i", $this->uid_to);
			$stmt->execute();
			$stmt->store_result();
			$stmt->close();
			
			return true;
		}
		return false;
	}
	private function updateMessage(){
		return false;
	}

	public static function getMessagesForUser($uid, $getToUser = true){
		
		if($getToUser){
			$stmt = Database::getDB()->prepare("
				SELECT * FROM messages as m, users as u
				WHERE m.m_uid_to = ? AND m.m_uid_from = u.u_uid
			");
		}
		else{
			$stmt = Database::getDB()->prepare("
				SELECT * FROM messages as m, users as u
				WHERE m.m_uid_from = ? AND m.m_uid_to = u.u_uid
			");
		}
		$stmt->bind_param("i",$uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$msgs = array();
		
		while($row = $stmt->fetch_assoc()){
			$msgs[] = new Message($row);
		}
		
		$stmt->close();
		
		return $msgs;
	}
	public function deleteMessage(){
		if(!$this->mid) return false;
		
		$stmt = Database::getDB()->prepare("
			DELETE FROM messages WHERE m_mid = ?
		");
		$stmt->bind_param("i", $this->mid);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			return true;
		}
		return false;
	}
}
?>