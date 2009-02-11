<?php
class community_controller{
	public function index(){
		RoutingEngine::getSmarty()->assign("users_all", User::getListOfUsers());
		RoutingEngine::getSmarty()->assign("users_friends", User::$current_user->getFriends());
	}
	public function view_user(){
		if(!isset($_GET["uid"])){
			header("Location: http://{$_SERVER['SERVER_NAME']}/community/");
			exit;
		}
		
		$uid = $_GET["uid"];
		
		//get route data for the user.
		$routes = Route::getRoutesForUser($uid, 5);
		//get training data for the user.
		$t_items = TrainingLog::getItemsForUserPaged($uid, 4);
		//get log data for the user.
		$l_items = Log::getAllActivityForUserPaged($uid, 5);
		
		RoutingEngine::getSmarty()->assign("r_query", "u_uid={$uid}&page=1&count=5");
		RoutingEngine::getSmarty()->assign("t_query", "u_uid={$uid}&page=1&count=5");
		RoutingEngine::getSmarty()->assign("user_routes", $routes); 
		RoutingEngine::getSmarty()->assign("user_training", $t_items); 
		RoutingEngine::getSmarty()->assign("user_log", $l_items); 
		RoutingEngine::getSmarty()->assign("user",User::fromUid($uid));
	}
	public function add_friend(){
		if(!isset($_POST["f_uid"])){
			echo 0;
			exit;
		}
		$friend_uid = $_POST["f_uid"];
		echo User::$current_user->addFriend($friend_uid);
	}
}
?>