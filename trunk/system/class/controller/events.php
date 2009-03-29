<?php
class Controller_Events{
	function index(){

	}
	function view(){
		if(!isset($_GET["eid"])){
			Notification::add("Redirect because no event was found with that ID");
			Page::redirect("/events");
		}
		$event = Event::find($_GET["eid"]);
		
		RoutingEngine::getSmarty()->assign("event_view", $event);
	}
	function create(){
		$types = Event::getTypes();
		
		RoutingEngine::getSmarty()->assign("event_types", $types);
		
		if(isset($_GET["eid"])){
			$eid = $_GET["eid"];
			$event = Event::find($eid);
			
			if($event->uid == User::$current_user->uid){
				RoutingEngine::getSmarty()->assign("event", $event);
			}
		}
	}
	function action_create(){
		$event = new Event($_POST);
		$event->uid = User::$current_user->uid;
		if($event->create()){
			Notification::add("Event created (updated) successfully.");
			Page::redirect("/events/view?eid={$event->eid}");
		}
		
		Notification::add("Event not created.  Please try again.");
		Page::redirect("/events/create");
	}
	function edit(){
		Notification::add("Page not created yet.");
	}
	function delete(){
		//check permissions
		if(!isset($_POST["e_eid"])){
			Notification::add("Could not delete the event.");
			Page::redirect("/events");
		}
		
		$event = new Event($_POST);
		if($event->delete()){
			Notification::add("Event was deleted.");
			Page::redirect("/events");
		}
		
		Notification::add("Error deleting.");
		Page::redirect("/events");
	}
	function join(){
		if(!isset($_POST["e_eid"])){
			RoutingEngine::returnAjax("Error.");
		}
		$eid = $_POST["e_eid"];
		$result = Event::addAttendeeToEvent($eid);
		
		RoutingEngine::returnAjax($result, true);
	}
	function leave(){
		if(!isset($_POST["e_eid"])){
			RoutingEngine::returnAjax("Error.");
		}
		$eid = $_POST["e_eid"];
		$result = Event::removeAttendeeFromEvent($eid);
		
		RoutingEngine::returnAjax($result, true);
	}
}
?>