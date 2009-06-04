<?php
/**
 * Confirmation
 * 
 * @author Byron Wall
 *
 */
class Confirmation extends Object
{
	public $cid;
	public $uid_to;
	public $uid_from;
	public $date_created;
	public $type;
	public $message;
	public $gid;
	public $eid;
	public $type_desc;
	
	/**
	 * @var User
	 */
	public $user_to;
	/**
	 * @var User
	 */
	public $user_from;
	public $group;
	public $event;
	
	private $_deleted = false;
	
	public function __construct($arr = null, $arr_pre = "c_")
	{
		parent::__construct($arr, $arr_pre);
		
		if($this->date_created){
			$this->date_created = strtotime($this->date_created);
		}
	}
	
	/**
	 * Create
	 *
	 * @return mixed
	 */
	public function create()
	{
		$stmt = Database::getDB()->prepare("
			INSERT INTO confirmations
			SET
				c_uid_from = ?,
				c_uid_to = ?,
				c_type = ?,
				c_message = ?,
				c_date_created = NOW()
		");
		$stmt->bind_param("iiis", $this->uid_from, $this->uid_to, $this->type, $this->message);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		if($stmt->insert_id){
			$this->cid = $stmt->insert_id;
		}
		$stmt->close();
		
		return $rows;		
	}
	/**
	 * Delete
	 *
	 * @return mixed
	 */
	public function delete()
	{
		$stmt = Database::getDB()->prepare("
			DELETE FROM confirmations
			WHERE
				c_cid = ?
		");		
		$stmt->bind_param("i", $this->cid);
		$stmt->execute() or die($stmt->error);
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		if($rows == 1){
			$this->_deleted = true;
			return true;
		}
		return false;
	}
	/**
	 * Process
	 *
	 * @param bool $shouldConfirm	Whether or not to allow the confirmation
	 * 
	 * @return mixed
	 */
	public function process($shouldConfirm = false)
	{
		if(!$shouldConfirm){
			return $this->delete();
		}
		switch($this->type){
			case 1: //Friend request
				$success = User::$current_user->addFriend($this->uid_from);				
				break;
		}
		
		if($success){
			return $this->delete();
		}
		return false;
	}
	
	public static function fetchForUser($uid, $shouldBeToUser = true){
		$user_field = ($shouldBeToUser)?"c_uid_to":"c_uid_from";
		
		$stmt = Database::getDB()->prepare("
			SELECT confirmations.*, c_type_desc, uto.u_uid as uto_uid, 
				uto.u_username as uto_username, ufrom.u_uid as ufrom_uid, ufrom.u_username as ufrom_username
			FROM confirmations
			JOIN confirmations_types USING (c_type)
			JOIN users as uto ON uto.u_uid = c_uid_to
			JOIN users as ufrom ON ufrom.u_uid = c_uid_from
			WHERE
				{$user_field} = ?
		");		
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$stmt->store_result();
		
		$results = array();
		while($row = $stmt->fetch_assoc()){
			$confirmation = new Confirmation($row);
			$confirmation->user_to = new User($row, "uto_");
			$confirmation->user_from = new User($row, "ufrom_");
			$results[$row["c_type_desc"]][] = $confirmation;
		}
		$stmt->close();
		return $results;
	}
	/**
	 * fetch
	 * 
	 * Returns a complete Confirmation object given an id.
	 *
	 * @param int $cid		ID of confirmation
	 * @return Confirmation	Object with data
	 */
	public static function fetch($cid)
	{
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM confirmations
			WHERE
				c_cid = ?
		");		
		$stmt->bind_param("i", $cid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$stmt->close();
		
		if($row){
			return new Confirmation($row);
		}
		return false;
	}
}
?>