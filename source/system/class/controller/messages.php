<?php
class Controller_Messages{
	public function index(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		$convo_list = Message::getConvosForUser(User::$current_user->uid);
		RoutingEngine::getSmarty()->assign("convo_list", $convo_list);
	}
	public function view_convo(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		RoutingEngine::getInstance()->registerParams("convo_id");
		
		if(!isset($_GET["convo_id"])){
			Page::redirect("/messages");
		}
		$convo_id = $_GET["convo_id"];
		$message_list = Message::getMessagesForConvo($convo_id);
		
		if(count($message_list) == 0){
			Page::redirect("/messages");
		}
		
		RoutingEngine::getSmarty()->assign("message_list", $message_list);
		$output = RoutingEngine::getSmarty()->fetch("messages/_view_convo.tpl");
		
		echo($output);
		$read_count = Message::markConvoRead($convo_id);
		if($read_count > 0){
			Message::updateCount(User::$current_user->uid, -($read_count));
			User::$current_user->msg_new -= $read_count;
		}
		
		die;
	}
	public function create(){
		RoutingEngine::setPage("New Message | runnDAILY", "PV__300");
		$output = RoutingEngine::getSmarty()->fetch("messages/_create.tpl");
		echo $output;
		
		die;
	}
	public function delete(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		RoutingEngine::getInstance()->registerParams("convo_id");
		if(!isset($_GET["convo_id"])){
			Page::redirect("/messages");
		}
		$convo_id = $_GET["convo_id"];
		
		RoutingEngine::getSmarty()->assign("convo_id", $convo_id);
		$output = RoutingEngine::getSmarty()->fetch("messages/_delete.tpl");
		echo $output;
		
		die;
		
		exit;
	}
	
	public function actionCreate(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		$message = new Message($_POST);
		//TODO:add in error exception in case the message cannot be created
		if($message->create()){
			Message::updateCount($message->uid_to, 1);
		}
		Page::redirect("/messages");
	}
	
	public function actionReply(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		$message = new Message($_POST);
		//TODO:add in error exception in case the message cannot be created
		if($message->reply()){
			Message::updateCount($message->uid_to, 1);
		}
		Page::redirect("/messages");
	}
	
	public function actionDelete(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		if(!isset($_POST["msg_convo_id"])){
			Page::redirect("/messages");
		}
		
		$message = new Message($_POST);
		
		$read_count = Message::markConvoRead($message->convo_id);
		if($read_count > 0){
			Message::updateCount(User::$current_user->uid, -($read_count));
			User::$current_user->msg_new -= $read_count;
		}
		
		$message->delete();
		Page::redirect("/messages");
	}
	//TODO:remove the old controller functions
	/*
	public function create(){
		$msg = new Message($_POST);
		if($msg->createOrUpdateMessage()){
			Page::redirect("/messages");
		}
		Page::redirect("/");
	}*/
}
?>