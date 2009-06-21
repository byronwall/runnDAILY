<?php
//require_once 'PHPUnit/Framework.php';
 
class RouteTest extends PHPUnit_Framework_TestCase
{
	protected $route = null;
	
	public function setUp(){
		User::$current_user = new User();
		User::$current_user->uid = 1;
	}
	public function testCreate(){
		$this->route = new Route();
		$this->route->name = "Test Route";
		$this->route->start_lat = 30.0;
		$this->route->start_lng = 30.0;
		$this->route->distance = 30.0;
		$this->route->points = "route points";
		
		$result = $this->route->createRoute();
		$this->assertTrue($result);
	}
	
	public function testUpdate(){
		$this->route = Route::fromRouteIdentifier(30);
		$this->route->distance = 50;
		$result = $this->route->updateRoute();
		$this->assertTrue($result);
	}
	
	/**
	 * This test should throw an exception because the route has training entries.
	 *
	 * @return unknown_type
	 */
	public function testDeleteWithTraining(){
		$this->route = Route::fromRouteIdentifier(30);
		$this->setExpectedException("SiteException");
		$this->route->deleteRouteSecure($this->route->id, $this->route->uid);
	}
	public function testDeleteWithoutTraining(){
		$this->route = Route::fromRouteIdentifier(39);
		$result = $this->route->deleteRouteSecure($this->route->id, $this->route->uid);
		$this->assertGreaterThan(0, $result);
	}
}