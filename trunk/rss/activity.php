<?php
require("../lib/config.php");

if(!isset($_GET["username"])){
	exit;
}

$user = User::fromUsername($_GET["username"]);
$a_items = Log::getActivityForUserByAid($user->uid, array(300,102,100,302));
$feed = new RssFeed("Training entries for {$user->username}", "http://{$_SERVER["SERVER_NAME"]}", "Includes the activity for a given user.");
$feed->pubDate = date(DATE_RSS);
$feed->defineImageForFeed(new RssImage("running logo", "http://{$_SERVER["SERVER_NAME"]}", "http://{$_SERVER["SERVER_NAME"]}/img/logo.png"));

foreach($a_items as $item){
	$rss_item = new RssItem();
	$rss_item->guid = "activity_{$item->id}";
	$rss_item->link = "http://{$_SERVER["SERVER_NAME"]}/community/view_user.php?uid={$item->uid}";
	$rss_item->description = "{$user->username} {$item->desc} {$item->route->name} on {$item->datetime}";
	$rss_item->pubDate = date(DATE_RSS, strtotime($item->datetime));
	$rss_item->title = "{$user->username} logged an entry on ".$item->datetime;
	
	$feed->addItemToFeed($rss_item);
}
//die(var_dump($a_items));

$smarty->assign("RssFeed", $feed);

$rss_out = $smarty->fetch("generic/rss.tpl");

header("Content-Type: application/rss+xml");
echo $rss_out;
?>