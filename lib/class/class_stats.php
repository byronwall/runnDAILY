<?php
class Stats extends Object{
	public $date;
	public $users;
	public $routes;
	public $routes_parent;
	public $trainings;

	public static function insertStats(){
		$stmt = database::getDB()->prepare("
			INSERT INTO stats
			SELECT
				NOW() as s_date,
				(SELECT COUNT(*)FROM users) as s_users,
				(SELECT COUNT(*) FROM routes) as s_routes,
				(SELECT COUNT(*) FROM routes WHERE r_rid_parent IS NULL) as s_routes_parent,
				(SELECT COUNT(*) FROM training_times) as s_trainings
		");
		$stmt->execute();
		$stmt->store_result();

		$rows = $stmt->affected_rows;
		$stmt->close();
		if($rows == 1){
			return true;
		}
		return false;
	}
	public static function getRecentStats($limit = 10){
		$stmt = database::getDB()->prepare("
			SELECT *
			FROM stats
			LIMIT ?
		");
		$stmt->bind_param("i", $limit);
		$stmt->execute();
		$stmt->store_result();
		
		$stats = array();
		
		while($row =$stmt->fetch_assoc()){
			$stats[] = new Stats($row, "s_");
		}
		return $stats;
	}
}
?>