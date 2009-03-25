<?php
class Controller_Home{
	public function index(){
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
	public function modules(){
		$modules = array();
		
		$sel_type=array_safe($_GET, "loc", null);
		
		$types = array("home","routes", "training", "community");
		
		foreach($types as $type){
			$var = $type."_modules";
			$temp = explode(",", User::$current_user->$var);
			foreach($temp as $item){ $modules[$type][$item] = true; }
		}
		
		RoutingEngine::getSmarty()->assign("user_modules", $modules);
		RoutingEngine::getSmarty()->assign("modules", Module::getAllModules($sel_type));
	}
}
?>