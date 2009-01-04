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
	public $activity_desc;
	
	/*optional parameters*/
	public $r_name;
	
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
	
	public static function getRouteActivityForUser($uid){
		$stmt = database::getDB()->prepare("SELECT logs.l_id, logs.l_datetime, logs.l_uid, logs_actions.l_cid, logs.l_aid, logs.l_xid, logs.l_rid, logs.l_tid, logs.l_gid, routes.r_name FROM logs JOIN logs_actions ON logs.l_aid = logs_actions.l_aid JOIN routes ON logs.l_rid = routes.r_id WHERE logs.l_uid = ? AND logs_actions.l_cid = 1") or die ($stmt->error);
		$stmt->bind_param("i", $uid) or die ($stmt->error);
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
		$log->familiar = Log::reckonDate($log->datetime);
		$log->activity_desc = Log::generateActivityDesc($log->aid);
		
		if ($route){
			$log->r_name = $row["r_name"];
		}
			
		return $log;
	}
	
	public static function generateActivityDesc($aid){
		switch($aid){
			case 100:
				return ("created the route");
				break;
		}
	}
}
?>