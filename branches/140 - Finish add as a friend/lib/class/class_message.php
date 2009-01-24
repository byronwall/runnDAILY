<?php
class Message{

	var $mid;
	var $uid_to;
	var $uid_from;
	var $user;
	var $msg;
	var $new;
	var $date;

	function __construct(){
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
		$stmt = database::getDB()->prepare("
			INSERT INTO messages(m_uid_to, m_uid_from, m_msg, m_date)
			VALUES(?,?,?,NOW())
		");
		$stmt->bind_param("iis", $this->uid_to, $this->uid_from, $this->msg) or die($stmt->error);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();

		if($rows == 1){
			$stmt = database::getDB()->prepare("
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
	public static function fromFetchAssoc($row, $getUserFrom = false){
		$message = new Message();

		$message->date = isset($row["m_date"])?$row["m_date"]:null;
		$message->mid = isset($row["m_mid"])?$row["m_mid"]:null;
		$message->msg = isset($row["m_msg"])?$row["m_msg"]:null;
		$message->new = isset($row["m_new"]) ? $row["m_new"] : true;
		$message->uid_from = isset($row["m_uid_from"])?$row["m_uid_from"]:null;
		$message->uid_to = isset($row["m_uid_to"])?$row["m_uid_to"]:null;
		
		if($getUserFrom){
			$message->user = User::fromFetchAssoc($row);
		}
		
		return $message;
	}
	public static function getMessagesForUser($uid, $getToUser = true){
		
		if($getToUser){
			$stmt = database::getDB()->prepare("
				SELECT * FROM messages as m, users as u
				WHERE m.m_uid_to = ? AND m.m_uid_from = u.u_uid
			");
		}
		else{
			$stmt = database::getDB()->prepare("
				SELECT * FROM messages as m, users as u
				WHERE m.m_uid_from = ? AND m.m_uid_to = u.u_uid
			");
		}
		$stmt->bind_param("i",$uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$msgs = array();
		
		while($row = $stmt->fetch_assoc()){
			$msgs[] = Message::fromFetchAssoc($row, true);
		}
		
		$stmt->close();
		
		return $msgs;
	}
	public function deleteMessage(){
		if(!$this->mid) return false;
		
		$stmt = database::getDB()->prepare("
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