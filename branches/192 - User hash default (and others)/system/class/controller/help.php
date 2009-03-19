<?php
class Controller_Help{
	public function index(){
		
	}
	public function view(){
		if(!isset($_GET["common"])){
			die;
		}
		$output = RoutingEngine::getSmarty()->fetch("help/_pages/{$_GET["common"]}.tpl");
		
		echo $output;
		die;
	}
}
?>