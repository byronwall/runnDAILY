<?php
class Elevation{
	
	/**
	 * Returns elevation data for points along a path.
	 * 
	 * @param array $points			Array containing data points with indices [lat,lng]
	 * @param int $points_per_mile	Number of data points per mile
	 * @return array				Elevation data as array with form [distance, elevation]
	 */
	public static function getElevationForPath($points, $points_per_mile = 5){
		if(!is_array($points)) return false;
		
		$mark_dist = 1 / $points_per_mile;
		
		$prev_dist = 0;
		$markers = array();
		
		$count = count($points);
		for($i=0; $i<$count-1; $i++){
			$point1 = $points[$i];
			$point2 = $points[$i+1];
			
			$dist = self::_getLatLngDistance($point1, $point2);			
			
			$avail_dist = $prev_dist + $dist;
			while($avail_dist >= $mark_dist){
				$cover_dist = ($prev_dist > 0)?($mark_dist - $prev_dist):$mark_dist;
				
				$scale = $cover_dist / $dist;
				
				$lat = $point1["lat"] + $scale * ($point2["lat"] - $point1["lat"]);
				$lng = $point1["lng"] + $scale * ($point2["lng"] - $point1["lng"]);				
				$markers[] = array("lat"=>$lat, "lng"=>$lng, "dist"=>(count($markers)+1)*$mark_dist);
				
				$avail_dist -= $mark_dist;
				$prev_dist = 0;
			}
			$prev_dist = $avail_dist;
		}
		$elevations = array();
		foreach($markers as $marker){
			if($elevation = Elevation::getElevation($marker["lat"], $marker["lng"])){
				$elevations[] = array($marker["dist"], $elevation);
			}
			else return false;
		}
		return $elevations;
	}
	
	/**
	 * Calculates the distance (in miles) between two points using the Haversine formula.
	 * 
	 * @param array $point1	Array with indices [lat,lng]
	 * @param array $point2	Array with indices [lat,lng]
	 * @return float		Distance in miles
	 */
	public static function _getLatLngDistance($point1, $point2){
		$lat1 = $point1["lat"];
		$lng1 = $point1["lng"];
		$lat2 = $point2["lat"];
		$lng2 = $point2["lng"];
		
		//$earth = 6371; //km change accordingly
		$earth = 3960; //miles
		
		//Point 1 cords
		$lat1 = deg2rad($lat1);
		$long1= deg2rad($lng1);
		
		//Point 2 cords
		$lat2 = deg2rad($lat2);
		$long2= deg2rad($lng2);
		
		//Haversine Formula
		$dlong=$long2-$long1;
		$dlat=$lat2-$lat1;
		
		$sinlat=sin($dlat/2);
		$sinlong=sin($dlong/2);
		
		$a=($sinlat*$sinlat)+cos($lat1)*cos($lat2)*($sinlong*$sinlong);		
		$c=2*asin(min(1,sqrt($a)));		
		$d=$earth*$c;
		
		return $d;
	}
	
	/**
	 * Returns the elevation for a single point.
	 * @param float $lat
	 * @param float $lng
	 * @return float|bool	Elevation at given point.  false indicates no data nearby.
	 */
	public static function getElevation($lat, $lng){
		if($points = self::_getNearestElevations($lat, $lng, 3)){		
			$elevation = self::_interpolateThreePoints($points, $lat, $lng);			
			return round($elevation, 2);
		}
		return false;
	}
	
	/**
	 * Returns the elevations of the given number of nearby points.
	 * @param float $lat
	 * @param float $lng
	 * @param int $points
	 * @return array|bool	Array contains data points with indices [lat,lng,elevation].  false indicates failure
	 */
	private static function _getNearestElevations($lat, $lng, $points = 3){
		$lng_dup = $lng;
		$lat_dup = $lat;
		
		$stmt = Database::getDB()->prepare("
			SELECT 
				lat, lng, elevation,
				SQRT( POW(lat - ?, 2) + POW(lng - ?, 2) ) as distance
			FROM elevation
			WHERE
				ABS(lat - ?) < 0.002 AND
				ABS(lng - ?) < 0.002
			ORDER BY distance ASC
			LIMIT ?
		");
		$stmt->bind_param("ddddi", $lat, $lng, $lat_dup, $lng_dup, $points);
		$stmt->execute();
		$stmt->store_result();
		
		$rows = $stmt->num_rows();
		
		if($stmt->num_rows()){		
			$results = array();
			while($row = $stmt->fetch_assoc()){
				$results[] = $row;
			}		
			return $results;
		}
		
		return false;
	}
	/**
	 * Function interpolates three data points using a parameterized plane.  The z-value
	 * of the final point is determined from the plane.
	 * 
	 * @param array $points	Array containing three points with indices lat, lng, elevation
	 * @param float $lat	Latitude of point with unknown elevation	
	 * @param float $lng	Longitude of point with unknown elevation
	 * @return float|bool	Float indicating calculated elevation.  false indicates failure.
	 */
	private static function _interpolateThreePoints($points, $lat, $lng){
		if(count($points) != 3) return false;
		
		$point = $points[0];
		
		//build vectors
		$v = array(
			"lat"=>$points[1]["lat"] - $point["lat"], 
			"lng"=>$points[1]["lng"] - $point["lng"], 
			"elevation"=>$points[1]["elevation"] - $point["elevation"] 
		);
		$w = array(
			"lat"=>$points[2]["lat"] - $point["lat"], 
			"lng"=>$points[2]["lng"] - $point["lng"], 
			"elevation"=>$points[2]["elevation"] - $point["elevation"] 
		);
		
		//calculate determinate
		$det = $v["lat"]*$w["lng"] - $v["lng"]*$w["lat"];
		
		//determine parameters
		$s = ($w["lng"]*($lat - $point["lat"]) - $w["lat"]*($lng-$point["lng"])) / $det; 
		$t = (-$v["lng"]*($lat - $point["lat"]) + $v["lat"]*($lng-$point["lng"])) / $det;

		//interpolate for given point
		$elevation = $point["elevation"] + $s*$v["elevation"] + $t*$w["elevation"];
		
		return $elevation;		
	}
	/**
	 * Function adds the elevation data files to the database.  Currently it expects the
	 * file to reside in the SYSTEM_ROOT directory.  This will be changed.
	 * 
	 * @param string $filename	Root of filename to be imported
	 * @param int $skip			Ratio of points ignored:imported
	 * @return bool
	 */
	public static function addElevationToDatabase($filename, $skip = 4){
		//read the settings file
		$ini = array();
		$handle = fopen(SYSTEM_ROOT . "/{$filename}.hdr", "r");
		while($line = fgets($handle)){
			list($key, $value) = preg_split("/\s+/", trim($line), 2);
			$ini[$key] = $value;
		}
		fclose($handle);
		
		$rows = $ini["nrows"];
		$cols = $ini["ncols"];
		
		$xll_corner = $ini["xllcorner"];
		$yll_corner = $ini["yllcorner"];
		$size = $ini["cellsize"];
		
		$top = $yll_corner + $size * ($rows+1);
		$left = $xll_corner;
		
		//read the data file
		$handle = fopen(SYSTEM_ROOT . "/{$filename}.flt", "rb");
		$db = Database::getDB();
		$db->autocommit(false);
		
		$lat = $top;
		$lng = $left;		
		$elev = 0;
		
		$stmt = $db->prepare("INSERT INTO elevation(lat,lng,elevation) VALUES(?,?,?)");
		$stmt->bind_param("ddd", $lat, $lng, $elev);
		set_time_limit(120);
		for($i = 0;$i<$rows;$i++){
			$contents = fread($handle, $cols*4);
			if($i % $skip) continue;
			//unpack is 1-indexed
			$arr = unpack("f*", $contents);
			$lng = $left;
			for($j = 0; $j<$cols; $j+=$skip){
				$elev = $arr[$j+1];
				$stmt->execute();
				
				//moving to the right on the map
				$lng += $size;
			}
			
			//moving down the map
			$lat -= $size;
		}
		//everything is a single transaction in case something fails
		$db->commit();
		$db->autocommit(true);
		return true;
	}
}
?>