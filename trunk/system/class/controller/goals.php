<?php
class Controller_Goals{
	public function index(){
		$goals = Goal::getGoalsForUser(User::$current_user->uid);
		var_dump($goals);
	}
	public function create(){
		
	}
	
	public function view(){
		$goal = Goal::getGoalById($_GET['id']);
		var_dump($goal);
	}
	
	public function action_create(){
		$goal = new Goal($_POST);
		
		if($goal->createGoal()){
			Page::redirect("/goals/view?id=".$goal->id);
		}else{
			Page::redirect("/goals");
		}
	}
}
?>