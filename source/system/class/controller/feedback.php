<?php
class Controller_Feedback{
	public function create(){
		RoutingEngine::setPage("runnDAILY", "PV__400");
		$message = new Message($_POST);

		if($message->message){
			$message->uid_to = null;
			$message->subject = null;
			exit(json_encode($message->create()));
		}
		var_dump($message);
	}
	public function delete(){
		RoutingEngine::setPage("runnDAILY", "PV__100");
		$message = new Message($_POST);
		exit(json_encode($message->deleteByType(2)));
	}
}
?>