<?php
class Controller_Group{
	public function index(){
		Page::redirect("/community/");
	}

	public function view(){
		if(!isset($_GET["gid"])){
			Page::redirect("/community/");
		}
		$id = $_GET["gid"];
		$group = Group::fromGroupID($id);
		
		RoutingEngine::getSmarty()->assign("group_view", $group);
	}
}
?>