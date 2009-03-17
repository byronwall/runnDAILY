<?php
class Rss_Feed{
	public $title = "runndaily feed";
	public $link = "http://runndaily.com";
	public $description = "runndaily feed desc";
	public $pubDate;
	
	public $image;
	public $hasImage = false;
	public $items = array();
	
	function __construct($title, $link, $desc){
		$this->title = $title;
		$this->link = $link;
		$this->description = $desc;
	}
	
	public function addItemToFeed($rssItem){
		$this->items[] = $rssItem;
	}
	
	public function defineImageForFeed($rssImage){
		$this->hasImage = true;
		$this->image = $rssImage;
	}	
}
?>