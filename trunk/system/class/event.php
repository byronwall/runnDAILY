<?php
class Event extends Object{
	public $eid;
	public $uid;
	public $gid;
	public $name;
	public $desc;
	public $start_date;
	public $end_date;
	public $type_id;
	public $private = false;
	public $rid;
	public $location_lat;
	public $location_lng;
	
	public $type;
	
	function __construct($arr, $arr_pre = "e_"){
		parent::__construct($arr, $arr_pre);
		
		$this->start_date = strtotime($this->start_date);
		$this->end_date = isset($this->end_date)?strtotime($this->end_date):null;
	}
	
	/**
	 * Adds the current event into the database (or update if the eid is set).
	 *
	 * @return bool Whether or not the insert statement affected rows.
	 */
	function create(){
		if(isset($this->eid)){
			return $this->update();
		}
		if(isset($this->gid, $this->uid)){
			Notification::add("Cannot have a group and user id set.");
			return false;
		}
		
		$stmt = Database::getDB()->prepare("
			INSERT INTO events
			SET
				e_uid = ?,
				e_gid = ?,
				e_name = ?,
				e_desc = ?,
				e_start_date = ?,
				e_end_date = ?,
				e_type_id = ?,
				e_private = ?,
				e_rid = ?,
				e_location_lat = ?,
				e_location_lng = ?
		");
		$stmt->bind_param("iissssiiidd",
			$this->uid,
			$this->gid,
			$this->name,
			$this->desc,
			date("Y-m-d H:i:s", $this->start_date),
			date("Y-m-d H:i:s", $this->end_date),
			$this->type_id,
			$this->private,
			$this->rid,
			$this->location_lat,
			$this->location_lng
		);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$eid = $stmt->insert_id;
		if($eid){
			$this->eid = $eid;
		}
		$stmt->close();
		
		return ($rows > 0);
	}
	/**
	 * Updates the current event with the new information.
	 *
	 * @return bool	Whether or not the call affected any rows.
	 */
	function update(){
		$stmt = Database::getDB()->prepare("
			UPDATE events
			SET
				e_name = ?,
				e_desc = ?,
				e_start_date = ?,
				e_end_date = ?,
				e_type_id = ?,
				e_private = ?,
				e_rid = ?,
				e_location_lat = ?,
				e_location_lng = ?
			WHERE
				e_eid = ?
		");
		$stmt->bind_param("ssssiiiddi",
			$this->name,
			$this->desc,
			date("Y-m-d H:i:s", $this->start_date),
			date("Y-m-d H:i:s", $this->end_date),
			$this->type_id,
			$this->private,
			$this->rid,
			$this->location_lat,
			$this->location_lng,
			$this->eid
		);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows > 0);
	}
	/**
	 * Attempts to delete an event from the
	 *
	 * @return bool	Whether not the call affected any rows.
	 */
	function delete(){
		$stmt = Database::getDB()->prepare("
			DELETE FROM events
			WHERE
				e_eid = ?
		");
		$stmt->bind_param("i",
			$this->eid
		);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows > 0);
	}
	/**
	 * Attempts to add a user to an event
	 *
	 * @param int $eid	Event id to be added to
	 * @return bool		Whether or not there are affected rows
	 */
	static function addAttendeeToEvent($eid){
		if(!isset($eid)){
			Notification::add("Event ID must be set to add attendee");
			return false;
		}
		
		$stmt = Database::getDB()->prepare("
			INSERT INTO users_events
			SET
				e_eid = ?,
				u_uid = ?
		");
		$stmt->bind_param("ii", $eid, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows > 0);
	}
	/**
	 * Attempts to remove a user from an event.
	 *
	 * @param int $eid	Event id of the applicable event
	 * @return bool		Whether or not rows were affected
	 */
	static function removeAttendeeFromEvent($eid){
		if(!isset($eid)){
			Notification::add("Event ID must be set to remove attendee");
			return false;
		}
		
		$stmt = Database::getDB()->prepare("
			DELETE FROM users_events
			WHERE
				e_eid = ? AND
				u_uid = ?
		");
		$stmt->bind_param("ii", $eid, User::$current_user->uid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$rows = $stmt->affected_rows;
		$stmt->close();
		
		return ($rows > 0);
	}
	
	/**
	 * INCOMPLETE
	 * Will grab details of events.
	 *
	 * @param $eid
	 * @return unknown_type
	 */
	static function find($eid){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM events
			LEFT JOIN events_types USING(e_type_id)
			WHERE
				e_eid = ?
		");
		$stmt->bind_param("i", $eid);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$row = $stmt->fetch_assoc();
		$stmt->close();
		
		return new Event($row);
	}
	
	static function getTypes(){
		$stmt = Database::getDB()->prepare("
			SELECT e_type_id, e_type
			FROM events_types
		");
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$types = array();
		while($row = $stmt->fetch_assoc()){
			$types[$row["e_type_id"]] = $row["e_type"];
		}
		
		return $types;
	}
}
?>