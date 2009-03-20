<?php
class Controller_Group{
	public function index(){
		Page::redirect("/community/");
	}

	public function view(){
		if(!isset($_GET["gid"])){
			Page::redirect("/community/");
		}
		$gid = $_GET["gid"];
		$group = Group::fromGroupID($gid);
		RoutingEngine::getSmarty()->assign("group_view", $group);
		RoutingEngine::getSmarty()->assign("user_is_member", Group::isMember($gid));
		$anoun = Group::getAnnouncement($gid);
		if($anoun){
			RoutingEngine::getSmarty()->assign("group_view_anoun", $anoun);
		}
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
		Group::createAnnouncement();
	}
	
	public function join(){
		$gid = $_POST["gid"];
		Group::joinGroup($gid);
	}
	
	public function leave(){
		$gid = $_POST["gid"];
		Group::leaveGroup($gid);
	}
}
?>