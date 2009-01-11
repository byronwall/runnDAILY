<?php
require("../lib/config.php");

$feed = new RssFeed("runndaily.com", "http://byron.byroni.us", "running site");
$feed->defineImageForFeed(new RssImage("running logo", "http://byroni.us", "http://runndaily.com/img/logo.png"));
$feed->addItemToFeed(new RssItem("byron is cool", "001", "http://byroni.us", "byron is cool"));
$feed->addItemToFeed(new RssItem("byron is cool", "002", "http://byroni.us", "byron is cool"));
$feed->addItemToFeed(new RssItem("byron is cool", "003", "http://byroni.us", "byron is cool"));

$smarty->assign("RssFeed", $feed);

$rss_out = $smarty->fetch("generic/rss.tpl");

header("Content-Type: application/rss+xml");
echo $rss_out;
?>