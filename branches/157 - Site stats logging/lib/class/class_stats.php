<?php
class Stats{
	public static function insertStats(){
		$result  = database::getDB()->query("SELECT COUNT(*) as s_users FROM users");
		$result_arr = $result->fetch_assoc();
		$users = $result_arr["s_users"];
		$result->close();

		$result  = database::getDB()->query("SELECT COUNT(*) as s_routes FROM routes");
		$result_arr = $result->fetch_assoc();
		$users = $result_arr["s_users"];
		$result->close();
		
		$result  = database::getDB()->query("SELECT COUNT(*) as s_routes_parent FROM routes WHERE r_rid_parent IS NULL");
		$result_arr = $result->fetch_assoc();
		$users = $result_arr["s_users"];
		$result->close();
		
		$result  = database::getDB()->query("SELECT COUNT(*) as s_trainings FROM training_times");
		$result_arr = $result->fetch_assoc();
		$users = $result_arr["s_users"];
		$result->close();
		
		
	}
}
?>