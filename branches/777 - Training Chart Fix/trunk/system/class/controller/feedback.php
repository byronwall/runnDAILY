<?php
class Controller_Feedback{
	public function create(){
		$message = new Message($_POST);

		if($message->msg){
			$message->uid_to = 0;
			$message->uid_from = User::$current_user->uid;
			exit(json_encode($message->createOrUpdateMessage()));
		}
	}
	public function delete(){
		$message = new Message($_POST);
		exit(json_encode($message->deleteMessage()));
	}
}
?>