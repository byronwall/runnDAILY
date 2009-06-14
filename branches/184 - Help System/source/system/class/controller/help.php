<?php
class Controller_Help{
	public function index(){
		RoutingEngine::setPage("runnDAILY About", "PV__400");
	}
	public function view($common){
		RoutingEngine::setPage("runnDAILY About", "PV__400");
		
		$pages = array(
			"about_contact",
			"about_credits",
			"about_index",
			"community_index",
			"community_view_user",
			"home_index",
			"routes_view",
			"route_create",
			"route_index",
			"training_index"
		);
		
		if(in_array($common, $pages)){
			$output = RoutingEngine::getSmarty()->fetch("help/_pages/{$common}.tpl");
		}else{
			$feedback = new Message();
			$feedback->uid_from = User::$current_user->uid;
			$feedback->uid_to = null;
			$feedback->message = "Please create a help page for " . $_SERVER["HTTP_REFERER"];
			$feedback->subject = null;
			$feedback->type = 2;
			$feedback->create();
			$output = RoutingEngine::getSmarty()->fetch("help/_pages/none.tpl");
		}
		
		echo $output;
		die;
	}
}
?>