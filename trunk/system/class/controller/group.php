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
}
?>