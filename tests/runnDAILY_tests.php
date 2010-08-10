<?php
require_once LIB_ROOT.'/simpletest/autorun.php';
class runnDAILY_Suite extends TestSuite
{
	public function runnDAILY_Suite(){
		$this->TestSuite("runnDAILY Test Suite");
		$files = scandir(TEST_LOC);
		foreach($files as $file){
			if(substr($file, -8) == "Test.php"){
				$this->addFile(TEST_LOC."/{$file}");
			}
		}
	}
}
?>