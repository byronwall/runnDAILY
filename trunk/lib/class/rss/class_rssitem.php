<?php
class RssItem{
	public $title;
	public $guid;
	public $link;
	public $description;
	public $pubDate;
	
	function __construct($title = null, $guid = null, $link = null, $desc = null){
		$this->title = $title;
		$this->guid  = $guid;
		$this->link = $link;
		$this->description = $desc;
	}
}
?>