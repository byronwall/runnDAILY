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
		
		if(isset($_GET["convo_id"])){
			$message_list = Message::getMessagesForConvo($_GET["convo_id"]);
			RoutingEngine::getSmarty()->assign("message_list", $message_list);
			$output = RoutingEngine::getSmarty()->fetch("messages/_view_convo.tpl");
		}else{
			Page::redirect("/messages");
		}
		
		echo($output);
		die;
	}
	public function create(){
		RoutingEngine::setPage("New Message | runnDAILY", "PV__300");
		$output = RoutingEngine::getSmarty()->fetch("messages/_create.tpl");
		echo $output;
		
		die;
	}
	
	public function actionCreate(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
		$message = new Message($_POST);
		//TODO:add in error exception in case the message cannot be created
		if($message->create()){
			if(Message::updateCount($message->uid_to, 1)){
				Notification::add("Your message was successfully delivered.");
			}
		}
		Page::redirect("/messages");
	}
	
	public function actionReply(){
		RoutingEngine::setPage("Messages | runnDAILY", "PV__300");
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