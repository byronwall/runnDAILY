<?php
class Controller_Rss{
	public function activity(){
		if(!isset($_GET["username"])){
			exit;
		}
		
		$user = User::fromUsername($_GET["username"]);
		$a_items = Log::getActivityByAid($user->uid, null, array(300,102,100,302));
		$feed = new Rss_Feed("Activity entries for {$user->username}", "http://{$_SERVER["SERVER_NAME"]}", "Includes the activity for a given user.");
		$feed->pubDate = date(DATE_RSS);
		$feed->defineImageForFeed(new Rss_Image("running logo", "http://{$_SERVER["SERVER_NAME"]}", "http://{$_SERVER["SERVER_NAME"]}/img/logo.png"));
		
		foreach($a_items as $item){
			$rss_item = new Rss_Item();
			$rss_item->guid = "activity_{$item->id}";
			$rss_item->link = "http://{$_SERVER["SERVER_NAME"]}/community/view_user/{$item->uid}";
			$rss_item->description = "{$user->username} {$item->desc} {$item->route->name} on {$item->datetime}";
			$rss_item->pubDate = date(DATE_RSS, strtotime($item->datetime));
			$rss_item->title = "{$user->username} logged an entry on ".$item->datetime;
			
			$feed->addItemToFeed($rss_item);
		}
		//die(var_dump($a_items));
		
		RoutingEngine::getSmarty()->assign("RssFeed", $feed);
		
		$rss_out = RoutingEngine::getSmarty()->fetch("generic/rss.tpl");
		
		header("Content-Type: application/rss+xml");
		echo $rss_out;
	}
	public function training(){
		if(!isset($_GET["username"])){
			exit;
		}
		
		$user = User::fromUsername($_GET["username"]);
		
		$t_items = TrainingLog::getItemsForUser($user->uid);
		
		$feed = new Rss_Feed("Training entries for {$user->username}", "http://{$_SERVER["SERVER_NAME"]}", "Includes the training data for a given user.");
		$feed->pubDate = date(DATE_RSS);
		$feed->defineImageForFeed(new Rss_Image("running logo", "http://byroni.us", "http://runndaily.com/img/logo.png"));
		
		foreach($t_items as $item){
			$rss_item = new Rss_Item();
			$rss_item->guid = "http://{$_SERVER["SERVER_NAME"]}/training/view/{$item->tid}";
			$rss_item->link = "http://{$_SERVER["SERVER_NAME"]}/training/view/{$item->tid}";
			$rss_item->description = "{$item->date} : {$item->distance} miles";
			$rss_item->pubDate = date(DATE_RSS, $item->date);
			$rss_item->title = "{$user->username} logged an entry on ".date("F j, Y, g:i a", $item->date);
			
			$feed->addItemToFeed($rss_item);
		}
		//die(var_dump($feed));
		
		RoutingEngine::getSmarty()->assign("RssFeed", $feed);
		
		$rss_out = RoutingEngine::getSmarty()->fetch("generic/rss.tpl");
		
		header("Content-Type: application/rss+xml");
		echo $rss_out;
	}
}
?>