<?php
class Log{
	public static function insertItem($uid, $aid, $xid, $rid, $tid, $gid){
		$stmt = database::getDB()->stmt_init();
		$stmt->prepare("INSERT INTO running.logs (l_id, l_datetime, l_uid, l_aid, l_xid, l_rid, l_tid, l_gid) VALUES(NULL, NOW(), ?, ?, ?, ?, ?, ?)") or die($stmt->error);
		$stmt->bind_param("iiiiii", $uid, $aid, $xid, $rid, $tid, $gid) or die ($stmt->error);
		
		$stmt->execute() or die($stmt->error);
		
		if($stmt->affected_rows == 1){
			$stmt->close();
			return true;
		}
		
		return false;
		
	}
	
	function createNewRoute(User $user, $distance, $points, $comments, $name, $start_lat, $start_lng){
		$stmt = database::getDB()->stmt_init();
		$stmt->prepare("INSERT INTO running.routes (r_uid ,r_distance ,r_points ,r_description ,r_name ,r_creation, r_start_lat, r_start_lng) VALUES(?, ?,?,?,?, NOW(),?,?)") or die($stmt->error);
		$stmt->bind_param("idsssdd", $user->userID, $distance, $points, $comments, $name, $start_lat, $start_lng) or die($stmt->error);

		$stmt->execute() or die($stmt->error);
		
		if($stmt->affected_rows == 1){
			$ins_id = $stmt->insert_id;
			$stmt->close();
			$user->logActivity("route", "created route ". $ins_id);
			return $ins_id;
		}
		return false;
	}	
}
?>