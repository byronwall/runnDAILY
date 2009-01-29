<?php
class help_controller{
	public function index(){
		
	}
	public function view(){
		if(!isset($_GET["common"])){
			exit;
		}
		Page::getSmarty()->assign("content", Page::getSmarty()->fetch("help/_pages/{$_GET["common"]}.tpl"));
	}
}
?>