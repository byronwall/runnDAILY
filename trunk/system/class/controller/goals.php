<?php
class Controller_Goals{
	public function index(){
		
	}
	public function create(){
		
	}
	
	public function view(){
		
	}
	
	public function action_create(){
		$goal = new Goal($_POST);
		
		if($goal->createGoal()){
			Page::redirect("/goals/success".$goal->id);
		}else{
			Page::redirect("/goals");
		}
	}
}
?>