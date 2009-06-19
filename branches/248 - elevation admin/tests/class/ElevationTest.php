<?php
class ElevationTest extends PHPUnit_Framework_TestCase{
	/**
	 * This test is to help determine the parameters for the nearest
	 * elevation search.
	 *
	 * @return unknown_type
	 */
	public function testGetNearestReturnCount(){
		$lat = 39.90574640039131;
		$lng = -86.18072318611667;
		$points = 3;
		
		$data = Elevation::_getNearestElevations($lat, $lng, 3);
		
		//make sure we have data
		$this->assertNotEquals(false, $data);		
		//make sure we have enough points
		$this->assertGreaterThanOrEqual($points, count($data));
		//make sure we do not have too many
		$this->assertLessThan(6, count($data));
	}
	//need a unit test for looking up a latlng elevation
	//need a unit test for adding data to the db
	//need a unit test for packing a file
	 
}
?>