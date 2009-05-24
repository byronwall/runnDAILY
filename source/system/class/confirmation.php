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
	
	public function __construct($arr = null, $arr_pre = "c_")
	{
		parent::__construct($arr, $arr_pre);
	}
	
	/**
	 * Create
	 *
	 * @return mixed
	 */
	public function create()
	{
		throw new Exception("Create is not implemented.");
	}
	/**
	 * Delete
	 *
	 * @return mixed
	 */
	public function delete()
	{
		throw new Exception("Delete is not implemented.");
		
	}
	/**
	 * Process
	 *
	 * @param $shouldConfirm
	 * @return mixed
	 */
	public function process($shouldConfirm = false)
	{
		throw new Exception("Process is not implemented.");
	}
	/**
	 * fetchForUser
	 * 
	 * Returns the confirmation entries for a given user grouped by type
	 *
	 * @param int $uid	User id for the given user
	 * @return array	Array of Confirmation objects grouped by [type][Item]
	 */
	public static function fetchForUser($uid)
	{
		$stmt = Database::prepare("
			SELECT *
			FROM confirmations as c
			JOIN confirmations_types as ct ON ct.c_type = c.c_type 
			WHERE
				c_uid_to = ?
		");		
		$stmt->bind_param("i", $uid);
		$stmt->execute();
		$stmt->store_result();
		
		$results = array();
		while($row = $stmt->fetch_assoc()){
			$results[$row["c_type"]][] = new Confirmation($row);
		}
		$stmt->close();
		return $results;
	}
}
?>