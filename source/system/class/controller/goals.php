<?php
class Controller_Goals{
	public function index(){
		RoutingEngine::setPage("runnDAILY Goals", "PV__300");
		$goals = Goal::getGoalsForUser(User::$current_user->uid);
		RoutingEngine::getSmarty()->assign("goal_list", $goals);
	}
	public function create(){
		RoutingEngine::setPage("runnDAILY Create Goal", "PV__300");
	}
	
	public function view($id){
		if(!isset($id)) Page::redirect("/goals");
		RoutingEngine::setPage("runnDAILY View Goal", "PV__300");
		$goal = Goal::getGoalById($id);
		$training_items = TrainingLog::getItemsForUserForGoalView(User::$current_user->uid, $goal->start, $goal->end);
		$goal->buildGoalDataUsingTrainingItems($training_items);
		RoutingEngine::getSmarty()->assign("goal", $goal);
		RoutingEngine::getSmarty()->assign("training_items", $training_items);
	}
	
	public function action_create(){
		RoutingEngine::setPage("runnDAILY", "PV__300");
		$goal = new Goal($_POST);
		if($goal->createGoal()){
			Goal::updatePercentForList(array(0 => array("go_id" => $goal->id)));
			Page::redirect("/goals/view/{$goal->id}");
		}else{
			Page::redirect("/goals");
		}
	}
}
?>