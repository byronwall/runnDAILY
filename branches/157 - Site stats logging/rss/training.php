<?php
require("../lib/config.php");

if(!isset($_GET["username"])){
	exit;
}

$user = User::fromUsername($_GET["username"]);

$t_items = TrainingLog::getItemsForUser($user->userID);

$feed = new RssFeed("Training entries for {$user->username}", "http://{$_SERVER["SERVER_NAME"]}", "Includes the training data for a given user.");
$feed->pubDate = date(DATE_RSS);
$feed->defineImageForFeed(new RssImage("running logo", "http://byroni.us", "http://runndaily.com/img/logo.png"));

foreach($t_items as $item){
	$rss_item = new RssItem();
	$rss_item->guid = "http://{$_SERVER["SERVER_NAME"]}/training/view.php?tid={$item->tid}";
	$rss_item->link = "http://{$_SERVER["SERVER_NAME"]}/training/view.php?tid={$item->tid}";
	$rss_item->description = "{$item->date} : {$item->distance} miles";
	$rss_item->pubDate = date(DATE_RSS, $item->date);
	$rss_item->title = "{$user->username} logged an entry on ".date("F j, Y, g:i a", $item->date);
	
	$feed->addItemToFeed($rss_item);
}
//die(var_dump($feed));

$smarty->assign("RssFeed", $feed);

$rss_out = $smarty->fetch("generic/rss.tpl");

header("Content-Type: application/rss+xml");
echo $rss_out;

?>