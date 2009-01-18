<?php

/*This class is currently undocumented and provided as-is.
 *Additional functions are required in order to improve the
 *logic and effeciency of the entire class.*/

class Log{
	/*standard parameters*/
	public $aid;
	public $xid;
	public $rid;
	public $tid;
	public $gid;
	public $datetime;
	public $familiar;
	public $desc;
	
	/*optional parameters*/
	public $route;
	
	public static function insertItem($uid, $aid, $xid, $rid, $tid, $gid){
		$stmt = database::getDB()->stmt_init();
		$stmt->prepare("INSERT INTO running.logs (l_id, l_datetime, l_uid, l_aid, l_xid, l_rid, l_tid, l_gid) VALUES(NULL, NOW(), ?, ?, ?, ?, ?, ?)") or die($stmt->error);
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
		$stmt = database::getDB()->prepare("
			SELECT *
			FROM logs as l
			JOIN logs_actions as la ON l.l_aid = la.l_aid
			JOIN routes as r ON l.l_rid = r.r_id
			WHERE
				l.l_uid = ? AND
				la.l_cid = 1
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
			$recent_list[] = Log::fromFetchAssoc($row, true);
		}
		
		$stmt->close();
		
		return $recent_list;
	}
	
	public static function getAllActivityForUser($uid, $limit = 20){
		$stmt = database::getDB()->prepare("
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
			$recent_list[] = Log::fromFetchAssoc($row, true);
		}
		
		$stmt->close();
		
		return $recent_list;
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
	
	public static function fromFetchAssoc($row, $route = false){
		$log = new Log();
		
		$log->aid = $row["l_aid"];
		$log->xid = $row["l_xid"];
		$log->rid = $row["l_rid"];
		$log->tid = $row["l_tid"];
		$log->gid = $row["l_gid"];
		$log->datetime = $row["l_datetime"];
		$log->desc = $row["l_desc"];
		$log->familiar = Log::reckonDate($log->datetime);
		
		if ($route){
			$log->route = Route::fromFetchAssoc($row, false, false);
		}
			
		return $log;
	}
}
?>