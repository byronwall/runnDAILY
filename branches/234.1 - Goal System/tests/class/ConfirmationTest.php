<?php
//require_once 'PHPUnit/Framework.php';
 
class ConfirmationTest extends PHPUnit_Framework_TestCase
{
	protected $confirm;
	
	public function setUp(){
		$this->confirm = new Confirmation();
        $this->confirm->uid_to = 76;
        $this->confirm->uid_from = 1;
        $this->confirm->type = 1;
	}
    public function testConfirmationCreate()
    {
        $rows = $this->confirm->create();

        $this->assertNotNull($this->confirm->cid);
        $this->assertEquals($rows, 1);
    }
    
    public function testConfirmationProcess(){
    	$this->confirm->create();
    	
    	$user = new User();
    	$user->uid = 76;
    	User::$current_user = $user;
    	
    	$this->confirm->process(true);
    	
    	$this->assertEquals($user->checkFriendsWith(1), true);
    }
    
    public function testConfirmationDelete(){
    	$this->confirm->create();
    	$new_confirm = Confirmation::fetch($this->confirm->cid);
    	$this->assertNotNull($new_confirm->uid_from);
    	$this->assertEquals($new_confirm->uid_to, $this->confirm->uid_to);
    	
    	$this->confirm->delete();
    	
    	$new_confirm = Confirmation::fetch($this->confirm->cid);
    	$this->assertFalse($new_confirm);
    }
}