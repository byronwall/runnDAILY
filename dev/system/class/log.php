<?php

/*This class is currently undocumented and provided as-is.
 *Additional functions are required in order to improve the
 *logic and effeciency of the entire class.*/

class Log extends Object{
	/*standard parameters*/
	public $id;
	public $uid;
	public $aid;
	public $xid;
	public $rid;
	public $tid;
	public $gid;
	public $cid;
	public $datetime;
	
	public $familiar;
	public $desc;
	
	/*optional parameters*/
	public $route;
	public $group;
	public $user;
	
	public function __construct($arr = null, $arr_pre = "l_"){
		parent::__construct($arr, $arr_pre);
		
		$this->familiar = Log::reckonDate($this->datetime);
		$this->route = new Route($arr);
	}
	
	public static function insertItem($uid, $aid, $xid, $rid, $tid, $gid){
		$stmt = Database::getDB()->stmt_init();
		$stmt->prepare("INSERT INTO logs (l_id, l_datetime, l_uid, l_aid, l_xid, l_rid, l_tid, l_gid) VALUES(NULL, NOW(), ?, ?, ?, ?, ?, ?)") or die($stmt->error);
		$stmt->bind_param("iiiiii", $uid, $aid, $xid, $rid, $tid, $gid) or die ($stmt->error);
		
		$stmt->execute() or die($stmt->error);
		
		if($stmt->affected_rows == 1){
			$stmt->close();
			return true;
		}
		$stmt->close();
		
		return false;
	}
	
	public static function getRouteActivityForUser($uid, $limit = 20){
		$stmt = Database::getDB()->prepare("
		SELECT u.u_uid, u.u_username, g_gid, g_name, r_id, r_name, xu.u_uid AS x_uid, xu.u_username AS x_username, l.l_aid
		FROM logs AS l
		JOIN logs_actions AS la ON l.l_aid = la.l_aid
		LEFT JOIN routes AS r ON l.l_rid = r.r_id
		LEFT JOIN groups AS g ON l_gid = g_gid
		LEFT JOIN users AS u ON l_uid = u.u_uid
		LEFT JOIN users AS xu ON xu.u_uid = l_xid
		WHERE
			l.l_uid = ?
		LIMIT
				?
		");
		$stmt->bind_param("ii", $uid, $limit) or die ($stmt->error);
		$stmt->execute();
		$stmt->store_result();
		
		$recent_list = array();
		
		while ($row = $stmt->fetch_assoc()){
			$recent_list[] = $row;
		}
		
		$stmt->close();
		
		return $recent_list;
	}
	
	public static function getAllActivityForUser($uid, $limit = 20){
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM logs as l
			JOIN logs_actions as la ON l.l_aid = la.l_aid
			JOIN routes as r ON l.l_rid = r.r_id
			WHERE
				l.l_uid = ?
			ORDER BY
				l.l_datetime DESC
			LIMIT
				?
		");
		$stmt->bind_param("ii", $uid, $limit) or die ($stmt->error);
		$stmt->execute();
		$stmt->store_result();
		
		$recent_list = array();
		
		while ($row = $stmt->fetch_assoc()){
			$recent_list[] = new Log($row);
		}
		
		$stmt->close();
		
		return $recent_list;
	}
	public static function getAllActivityForUserPaged($uid, $count=5, $page=0){
		$limit_lower = $page * $count;
		$limit_upper = $page * $count + $count;
		$stmt = Database::getDB()->prepare("
			SELECT *
			FROM logs as l
			JOIN logs_actions as la ON l.l_aid = la.l_aid
			JOIN routes as r ON l.l_rid = r.r_id
			WHERE
				l.l_uid = ?
			ORDER BY
				l_datetime DESC
			LIMIT
				?, ?
		");
		$stmt->bind_param("iii", $uid, $limit_lower, $limit_upper);
		$stmt->execute() or die($stmt->error);
		$stmt->store_result();
		
		$logs = array();
		
		while ($row = $stmt->fetch_assoc()){
			$logs[] = new Log($row);
		}
		
		$stmt->close();
		
		return $logs;
		
	}
	
	public static function reckonDate($datetime){
		$date_secs = strtotime($datetime);
		$days_since = idate("z") - idate("z", $date_secs);
		
		if ($days_since < 7){
			if ($days_since == 0){
				return ("This " . Log::evaluateTime($datetime));
			}elseif ($days_since == 1){
				return ("Yesterday " . Log::evaluateTime($datetime));
			}else{
				return (date("l", $date_secs) . " " . Log::evaluateTime($datetime));
			}
		}
		
		$weeks_since = idate("W") - idate("W", $date_secs);
		$months_since = idate("m") - idate("m", $date_secs);
		
		if ($months_since == 0){
			switch($weeks_since){
				case 1:
					return ("Last week");
					break;
				case 2:
					return ("Two weeks ago");
					break;
				case 3:
					return ("Three weeks ago");
					break;
				case 4:
					return ("Four weeks ago");
					break;
			}
		}elseif ($months_since == 1){
			return ("Last month");
		}elseif ($months_since > 1){
			return (date("F Y", $date_secs));
		}
	}
	
	public static function evaluateTime($datetime){
		$hour = date("G", strtotime($datetime));
		
		if ($hour < 12){
			return ("morning");
		}else if($hour >=12 && $hour < 18){
			return ("afternoon");
		}else{
			return ("evening");
		}
	}
	
	/**
	 * Function returns the log data for a given user with the given activity ids.
	 * @param $uid: user id
	 * @param $aids: array containing the l_aid to match (eg: "200,201,202")
	 * @return arrar(Log)
	 */
	public static function getActivityByAid($uid, $gid, $aids){
		if($uid){
			$field = "uid";
			$id = $uid;
		}else{
			$field = "gid";
			$id = $gid;
		}
		if(count($aids) == 0){
			return false;
		}
		$in_str = implode(",", $aids);
		
		$query = "
			SELECT u.u_uid, u.u_username, g_gid, g_name, r_id, r_name, xu.u_uid AS x_uid, xu.u_username AS x_username, l.l_aid, la.l_cid, l.l_datetime, la.l_desc, tt.t_tid
			FROM LOGS AS l
			JOIN logs_actions AS la ON l.l_aid = la.l_aid
			LEFT JOIN routes AS r ON l.l_rid = r.r_id
			LEFT JOIN groups AS g ON l.l_gid = g_gid
			LEFT JOIN users AS u ON l.l_uid = u.u_uid
			LEFT JOIN users AS xu ON xu.u_uid = l.l_xid
			LEFT JOIN training_times AS tt ON l.l_tid = tt.t_tid
			WHERE
				l.l_aid IN({$in_str}) AND
				l.l_{$field} = {$id}
			ORDER BY
				l_datetime DESC
		";
		
		$result = Database::getDB()->query($query);
				
		$items = array();
		
		while($row = $result->fetch_assoc()){
			$row["familiar"] = Log::reckonDate($row["l_datetime"]);
			$items[] = $row;
		}
		$result->close();
		
		return $items;
	}
	/**
	 * Function returns the log data for a given user with the given category ids.
	 * @param $uid: user id
	 * @param $aids: array containing the l_cid to match (eg: "1,2")
	 * @return arrar(Log)
	 */
	public static function getActivityByCid($uid, $gid, $cids){
		if($uid){
			$field = "uid";
			$id = $uid;
		}else{
			$field = "gid";
			$id = $gid;
		}
		if(count($cids) == 0){
			return false;
		}
		$in_str = $cids;
		if(is_array($cids)){
			$in_str = implode(",", $cids);
		}
		
		$query = "
			SELECT *
			FROM logs as l
			JOIN logs_actions as la USING(l_aid)
			LEFT JOIN routes as r ON l.l_rid = r.r_id
			WHERE
				la.l_cid IN({$in_str}) AND
				l.l_{$field} = {$id}
		";
		
		$result = Database::getDB()->query($query);
				
		$items = array();
		
		while($row = $result->fetch_assoc()){
			$items[] = new Log($row);
		}
		$result->close();
		
		return $items;
	}
}
?>