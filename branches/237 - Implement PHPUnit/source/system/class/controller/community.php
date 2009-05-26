<?php
class Controller_Community{
	public function index(){
		RoutingEngine::getSmarty()->assign("users_all", User::getListOfUsers());
		RoutingEngine::getSmarty()->assign("users_friends", User::$current_user->getFriends());
	}
	public function view_user(){
		if(!isset($_GET["uid"])){
			Page::redirect("/community");
		}
		$uid = $_GET["uid"];
		
		$routes = Route::getRoutesForUserInArray($uid, 50);
		//$routes_js = json_encode_null($routes);
		
		RoutingEngine::getSmarty()->assign("routes", $routes);
		//RoutingEngine::getSmarty()->assign("routes_js", $routes_js);
		
		$index_items = TrainingLog::getIndexItemsForUser($uid);
		//$json_data = TrainingLog::buildChartData($index_items);
		RoutingEngine::getSmarty()->assign("training_index_items", $index_items);
		//RoutingEngine::getSmarty()->assign("JSON_Chart_Data", $json_data);
		
		RoutingEngine::getSmarty()->assign("user",User::fromUid($uid));
	}
	public function add_friend(){
		if(!isset($_POST["f_uid"])){
			RoutingEngine::returnAjax(array("result"=>false), true);
		}
		$friend_uid = $_POST["f_uid"];
		$added = User::$current_user->addFriend($friend_uid);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax(array("result"=>$added), true);
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