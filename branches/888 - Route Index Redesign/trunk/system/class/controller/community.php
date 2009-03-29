<?php
class Controller_Community{
	public function index(){
		RoutingEngine::getSmarty()->assign("users_all", User::getListOfUsers());
		RoutingEngine::getSmarty()->assign("users_friends", User::getFriends());
	}
	public function view_user(){
		if(!isset($_GET["uid"])){
			Page::redirect("/community");
		}
		$uid = $_GET["uid"];
		
		$routes = Route::getRoutesForUser($uid, 5);
		$t_items = TrainingLog::getItemsForUserPaged($uid, 4);
		$l_items = Log::getAllActivityForUserPaged($uid, 5);
		
		User::$current_user->getFriends();
		
		RoutingEngine::getSmarty()->assign("r_query", "u_uid={$uid}&page=1&count=5");
		RoutingEngine::getSmarty()->assign("t_query", "u_uid={$uid}&page=1&count=5");
		RoutingEngine::getSmarty()->assign("user_routes", $routes);
		RoutingEngine::getSmarty()->assign("user_training", $t_items);
		RoutingEngine::getSmarty()->assign("user_log", $l_items);
		RoutingEngine::getSmarty()->assign("user",User::fromUid($uid));
	}
	public function add_friend(){
		if(!isset($_POST["f_uid"])){
			RoutingEngine::returnAjax(false);
		}
		$friend_uid = $_POST["f_uid"];
		$added = User::$current_user->addFriend($friend_uid);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax($added);
	}
	public function ajax_remove_friend(){
		if(!isset($_POST["f_uid"])){
			RoutingEngine::returnAjax(false);
		}
		$friend_uid = $_POST["f_uid"];
		$removed = User::$current_user->removeFriend($friend_uid);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax($removed);
	}
}
?>