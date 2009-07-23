<?php
class Controller_Home{
	public function index(){
		RoutingEngine::setPage("runnDAILY", "PV__400");
		
//		Template::compileAllTemplates();
	}
	public function login(){
		RoutingEngine::setPage("runnDAILY Login", "PV__400");
		if(RoutingEngine::getInstance()->requirePermission("PV__300")){
			Notification::add("You are already logged in.");
			Page::redirect("/");
		}		
	}
	//TODO:remove this function, new controller has been implemented
	/*
	public function messages(){
		//Page not supported yet.
		RoutingEngine::setPage("runnDAILY Messages", "PV__100");
		
		$msgs_to = Message::getMessagesForUser(User::$current_user->uid, true);
		$msgs_from = Message::getMessagesForUser(User::$current_user->uid, false);
		
		RoutingEngine::getSmarty()->assign("messages_to", $msgs_to);
		RoutingEngine::getSmarty()->assign("messages_from", $msgs_from);
	}
	*/
	public function register(){
		RoutingEngine::setPage("runnDAILY Registration", "PV__400");
	}
	public function settings(){
		RoutingEngine::setPage("runnDAILY Settings", "PV__300");
	}
}
?>