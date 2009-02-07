<?php
class message_controller{
	public function create(){
		$msg = new Message($_POST);
		if($msg->createOrUpdateMessage()){
			Page::redirect("/messages");
		}
		Page::redirect("/");
	}
}
?>