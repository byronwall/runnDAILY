<?php
class Rss_Image{
	public $title;
	public $link;
	public $url;
	public $width;
	public $height;
	public $description;
	
	function __construct($title, $link, $url){
		$this->title = $title;
		$this->link = $link;
		$this->url = $url;
	}
}
?>