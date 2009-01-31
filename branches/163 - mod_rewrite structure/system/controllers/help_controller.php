<?php
class help_controller{
	public function index(){
		
	}
	public function view(){
		if(!isset($_GET["common"])){
			exit;
		}
		RoutingEngine::getSmarty()->assign("content", RoutingEngine::getSmarty()->fetch("help/_pages/{$_GET["common"]}.tpl"));
	}
}
?>