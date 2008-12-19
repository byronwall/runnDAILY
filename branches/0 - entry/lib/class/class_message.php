<?php
class Message{
	var $msg;
	
	private $mysqli;
	
	function __construct($msg = NULL){
		$this->mysqli = database::getDB();
		$this->msg = $msg;
	}
	
	function createMessage(){
		
	}
}
?>