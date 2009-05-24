<?php
class Controller_Goals{
	public function index(){
		$goals = Goal::getGoalsForUser(User::$current_user->uid);
		RoutingEngine::getSmarty()->assign("goal_list", $goals);
	}
	public function create(){
		
	}
	
	public function view(){
		$goal = Goal::getGoalById($_GET['id']);
		$training_items = TrainingLog::getItemsForUserForGoalView(User::$current_user->uid, $goal->start, $goal->end);
		//var_dump($training_items);
		RoutingEngine::getSmarty()->assign("goal", $goal);
		RoutingEngine::getSmarty()->assign("training_items", $training_items);
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