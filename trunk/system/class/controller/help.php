<?php
class Controller_Help{
	public function index(){
		
	}
	public function view(){
		if(isset($_GET["common"])){
			$output = RoutingEngine::getSmarty()->fetch("help/_pages/{$_GET["common"]}.tpl");
		}else{
			$feedback = new Message();
			$feedback->uid_from = User::$current_user->uid;
			$feedback->uid_to = 0;
			$feedback->msg = "Please create a help page for " . $_SERVER["HTTP_REFERER"];
			$feedback->createOrUpdateMessage();
			$output = RoutingEngine::getSmarty()->fetch("help/_pages/none.tpl");
		}
		
		echo $output;
		die;
	}
}
?>