<?php
class home_controller{
	public function index(){
		if(User::$current_user->uid){
			$recent_activity = Log::getAllActivityForUser(User::$current_user->uid);
			RoutingEngine::getSmarty()->assign("recent_activity_list", $recent_activity);
		}
		
		RoutingEngine::getSmarty()->assign("body_id", "body_home");
	}
	public function login(){
		
	}
	public function messages(){
		$msgs_to = Message::getMessagesForUser(User::$current_user->uid, true);
		$msgs_from = Message::getMessagesForUser(User::$current_user->uid, false);
		
		RoutingEngine::getSmarty()->assign("messages_to", $msgs_to);
		RoutingEngine::getSmarty()->assign("messages_from", $msgs_from);
	}
	public function register(){
		
	}
	public function settings(){
		
	}
}
?>