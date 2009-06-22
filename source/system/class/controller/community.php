<?php
class Controller_Community{
	public function index(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		$users = User::sql()
			->select("u_username, u_uid, u_join")
			->limit(50)
			->orderby("u_join")
			->execute();
		//RoutingEngine::getSmarty()->assign("users_recent", User::getListOfUsers());
		RoutingEngine::getSmarty()->assign("users_recent", $users);
		RoutingEngine::getSmarty()->assign("users_friends", User::$current_user->getFriends());
	}
	public function view_user(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		RoutingEngine::getInstance()->registerParams("uid");
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
		//var_dump(User::$current_user);
		RoutingEngine::getSmarty()->assign("user",User::fromUid($uid));
	}
	public function add_friend(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		if(!isset($_POST["f_uid"])){
			RoutingEngine::returnAjax(array("result"=>false), true);
		}
		$friend_uid = $_POST["f_uid"];
		$added = User::$current_user->addFriend($friend_uid);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax(array("result"=>$added), true);
	}
	public function ajax_remove_friend(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		if(!isset($_POST["f_uid"])){
			RoutingEngine::returnAjax(array("result"=>false), true);
		}
		$friend_uid = $_POST["f_uid"];
		$removed = User::$current_user->removeFriend($friend_uid);
		RoutingEngine::getInstance()->persistUserData();
		RoutingEngine::returnAjax(array("result"=>$removed), true);
	}
	
	public function search(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		if(!isset($_POST["u_search"]) || $_POST["u_search"] == ""){
			echo("<p>Please enter a search term.</p>");
			exit;
		}
		$user_list = User::searchForUser($_POST["u_search"]);
		RoutingEngine::getSmarty()->assign("user_list", $user_list);
		$output = RoutingEngine::getSmarty()->fetch("community/_user_search_result.tpl");
		echo($output);
		
		exit;
	}
}
?>