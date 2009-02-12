<?php
class Module{
	public $content;
	public $id;
	public $title;
	public $size;
	
	function __construct($id = "", $title = "Module", $size = null, $content = ""){
		$this->content = $content;
		$this->id = $id;
		$this->title = $title;
		$this->size = $size;
	}
}
?>