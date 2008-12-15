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
}
?>