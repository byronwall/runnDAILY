<?php
class Message extends Object{

	public $id;
	public $convo_id;
	public $uid_to;
	public $uid_from;
	public $type;
	public $subject;
	public $message;
	public $date_created;
	public $gid;
	public $eid;
	public $new;

	function __construct($arr = null, $arr_pre = "msg_"){
		parent::__construct($arr, $arr_pre);
		
		if($this->date_created){
			$this->date_created = strtotime($this->date_created);
		}
	}
	
	function create(){
		//TODO:update this statement to account for events and groups
		
		//insert message into the message inbox table
		$stmt = Database::getDB()->prepare("
			INSERT INTO messages
			SET
				msg_uid_to = ?,
				msg_uid_from = ?,
				msg_type = ?,
				msg_subject = ?,
				msg_message = ?,
				msg_date_created = NOW()
		");
		$stmt->bind_param("iiiss", $this->uid_to, User::$current_user->uid, $this->type, $this->subject, $this->message);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$ins_id = $stmt->insert_id;
		$stmt->close();
		
		if ($rows != 1){
			return 0;
		}
		
		//update the message conversation id
		if(!$this->convo_id){
			$this->convo_id = $ins_id;
		}
		
		$stmt = Database::getDB()->prepare("
			UPDATE messages
			SET msg_convo_id = ?
			WHERE msg_id = ?
		");
		$stmt->bind_param("ii", $this->convo_id, $ins_id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows == 1);
	}
	
	function reply(){
		$stmt = Database::getDB()->prepare("
			INSERT INTO messages
			SET
				msg_convo_id = ?,
				msg_uid_to = ?,
				msg_uid_from = ?,
				msg_type = ?,
				msg_subject = ?,
				msg_message = ?,
				msg_date_created = NOW()
		");
		$stmt->bind_param("iiiiss", $this->convo_id, $this->uid_to, User::$current_user->uid, $this->type, $this->subject, $this->message);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows == 1);
	}
	
	function delete(){
		$stmt = Database::getDB()->prepare("
			UPDATE messages
			SET msg_active_to = 0
			WHERE msg_convo_id = ? AND msg_uid_to = ?
		");
		$stmt->bind_param("ii", $this->convo_id, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$stmt->close();
		
		$stmt = Database::getDB()->prepare("
			UPDATE messages
			SET msg_active_from = 0
			WHERE msg_convo_id = ? AND msg_uid_from = ?
		");
		$stmt->bind_param("ii", $this->convo_id, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$stmt->close();
	}
	
	function deleteByType($type_id){
		$stmt = Database::getDB()->prepare("
			UPDATE messages
			SET msg_active_to = 0
			WHERE msg_convo_id = ? AND msg_type = ?
		");
		$stmt->bind_param("ii", $this->convo_id, $type_id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows;
	}
	
	public static function updateCount($user_id, $offset){
		if($offset == "clear"){
			$stmt = Database::getDB()->prepare("
				UPDATE users
				SET u_msg_new = 0
				WHERE u_uid = ?
			");
			$stmt->bind_param("i", $user_id);
		}else{
			$stmt = Database::getDB()->prepare("
				UPDATE users
				SET u_msg_new = u_msg_new + ?
				WHERE u_uid = ?
			");
			$stmt->bind_param("ii", $offset, $user_id);
		}
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows == 1);
	}
	
	public static function getConvosForUser($user_id){
		$stmt = Database::getDB()->prepare("
			SELECT m1.msg_convo_id, m1.msg_uid_to, m1.msg_uid_from, m1.msg_subject, m1.msg_date_created, m1.msg_new, u1.u_username AS msg_username_to, u2.u_username AS msg_username_from
			FROM messages m1
			LEFT JOIN users u1 ON m1.msg_uid_to = u1.u_uid
			LEFT JOIN users u2 ON m1.msg_uid_from = u2.u_uid
			LEFT JOIN messages m2
			ON 
				m1.msg_convo_id = m2.msg_convo_id AND
				m1.msg_date_created < m2.msg_date_created
			WHERE
				m2.msg_convo_id IS NULL
				AND
				(
					(
						m1.msg_uid_to = ?
						AND m1.msg_active_to = 1
					)
					OR
					(
						m1.msg_uid_from = ?
						AND m1.msg_active_from = 1
					)
				)
				AND m1.msg_type = 1
			ORDER BY m1.msg_date_created DESC
		");
		
		$stmt->bind_param("ii", $user_id, $user_id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$convo_list = array();

		while($row = $stmt->fetch_assoc()){
			$convo_list[] = $row;
		}

		$stmt->close();

		return $convo_list;
	}
	
	public static function getMessagesForConvo($convo_id){
		$stmt = Database::getDB()->prepare("
			SELECT msg_convo_id, msg_uid_to, msg_uid_from, msg_subject, msg_message, msg_date_created, msg_new, u_username AS msg_username_from
			FROM messages
			LEFT JOIN users ON msg_uid_from = u_uid
			WHERE
				msg_convo_id = ?
				AND
				(
					(
						msg_uid_to = ? AND
						msg_active_to = 1
					)
					OR
					(
						msg_uid_from = ? AND
						msg_active_from = 1
					)
				)
			ORDER BY msg_date_created ASC
		");
		
		$stmt->bind_param("iii", $convo_id, User::$current_user->uid, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$message_list = array();

		while($row = $stmt->fetch_assoc()){
			$message_list[] = $row;
		}

		$stmt->close();

		return $message_list;
	}
	
	public static function markConvoRead($convo_id){
		$stmt = Database::getDB()->prepare("
			UPDATE messages
			SET msg_new = 0
			WHERE
				msg_convo_id = ? AND
				msg_uid_to = ?
		");
		$stmt->bind_param("ii", $convo_id, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return $rows;
	}
	
	public static function getMessagesByType($type_id){
		$stmt = Database::getDB()->prepare("
			SELECT m.*, u.u_username, u.u_uid
			FROM messages m
			LEFT JOIN users u ON u_uid = m.msg_uid_from
			WHERE
				msg_type = ? AND
				msg_active_to = 1
		");
		
		$stmt->bind_param("i", $type_id);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$message_list = array();

		while($row = $stmt->fetch_assoc()){
			$message_list[] = $row;
		}

		$stmt->close();

		return $message_list;
	}
}
?>