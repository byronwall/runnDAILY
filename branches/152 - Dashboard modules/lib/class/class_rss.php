<?php
class RssFeed{
	var $title = "runndaily feed";
	var $link = "http://runndaily.com";
	var $description = "runndaily feed desc";
	public $pubDate;
	
	var $image;
	var $hasImage = false;
	var $items = array();
	
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

class RssItem{
	var $title;
	var $guid;
	var $link;
	var $description;
	var $pubDate;
	
	function __construct($title = null, $guid = null, $link = null, $desc = null){
		$this->title = $title;
		$this->guid  = $guid;
		$this->link = $link;
		$this->description = $desc;
	}
}
class RssImage{
	var $title;
	var $link;
	var $url;
	var $width;
	var $height;
	var $description;
	
	function __construct($title, $link, $url){
		$this->title = $title;
		$this->link = $link;
		$this->url = $url;
	}
}

?>