<?php
class Controller_Group{
	/*
	 * This controller is missing all of the RoutingEngine::setPage calls.
	 * This is done on purpose to force admin privledges until we want these pages exposed.
	 * 
	 */
	public function index(){

	}

	public function view(){
		if(!isset($_GET["gid"])){
			Page::redirect("/community/");
		}
		$gid = $_GET["gid"];
		$group = Group::fromGroupID($gid);
		RoutingEngine::getSmarty()->assign("group_view", $group);
		RoutingEngine::getSmarty()->assign("user_is_member", Group::userIsMember($gid));
		RoutingEngine::getSmarty()->assign("user_can_edit", Group::userCanEdit($gid));
		RoutingEngine::getSmarty()->assign("group_view_anoun", Group::getAnnouncement($gid));
		RoutingEngine::getSmarty()->assign("group_view_member_list", Group::getMembers($gid));
		RoutingEngine::getSmarty()->assign("group_view_activity", Log::getActivityByAid(null, $gid, array(500)));
	}

	public function create(){

	}

	public function action_create(){
		$group = new Group($_POST);
		if($group->createGroup()){
			if(isset($_FILES['img_up'])){
				$updir = PUBLIC_ROOT . "/img/group/";
				$path_part = pathinfo($_FILES['img_up']['name']);
				$upfile = ($group->gid % 100) . "/" . $group->gid . "." . $path_part["extension"];
				if (move_uploaded_file($_FILES['img_up']['tmp_name'], $updir . $upfile)){
					if($group->updateImage($upfile)){
						Page::redirect("/group/view?gid=".$group->gid);
					}
				}
			}
		}
	}
	
	public function action_new_announcement(){
		$anoun = Group::createAnnouncement();
		
		echo json_encode($anoun);
		exit;
	}
	
	public function join(){
		$gid = $_POST["gid"];
		$result = Group::joinGroup($gid);
		
		echo json_encode($result);
		exit;
	}
	
	public function leave(){
		$gid = $_POST["gid"];
		$result = Group::leaveGroup($gid);
		
		echo json_encode($result);
		exit;
	}
}
?>