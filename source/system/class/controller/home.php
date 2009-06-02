<?php
class Controller_Home{
	public function index(){
		RoutingEngine::setPage("runnDAILY Home Page", "PV__400");
	}
	public function login(){
		RoutingEngine::setPage("runnDAILY Login", "PV__400");		
	}
	public function messages(){
		$msgs_to = Message::getMessagesForUser(User::$current_user->uid, true);
		$msgs_from = Message::getMessagesForUser(User::$current_user->uid, false);
		
		RoutingEngine::getSmarty()->assign("messages_to", $msgs_to);
		RoutingEngine::getSmarty()->assign("messages_from", $msgs_from);
	}
	public function register(){
		RoutingEngine::setPage("runnDAILY Registration", "PV__400");		
	}
	public function settings(){
		RoutingEngine::setPage("runnDAILY Settings", "PV__300");		
	}
}
?>