<?php
DEFINE("TEST_ROOT", dirname(__FILE__));
DEFINE("TEST_DATA", TEST_ROOT."/data");
DEFINE("TEST_LOC", TEST_ROOT."/class");
DEFINE("SYSTEM_ROOT", dirname(__FILE__)."/../source/system");
DEFINE("CLASS_ROOT", SYSTEM_ROOT."/class");

require_once 'PHPUnit/Framework.php';
require_once SYSTEM_ROOT."/config.php";

class runnDAILY_Suite extends PHPUnit_Framework_TestSuite
{
	public static function suite(){
		$suite = new runnDAILY_Suite();
		$files = scandir(TEST_LOC);
		foreach($files as $file){
			if(substr($file, -8) == "Test.php"){
				$suite->addTestFile(TEST_LOC."/{$file}");
			}
		}
		return $suite;
	}
    public function setUp(){
    	Database::getDB()->autocommit(false);
    }
    public function tearDown(){
    	//tries to undo all changes that were made.
    	//this fails if any code calls $db->commit()
    	Database::getDB()->rollback();
    }
}
?>