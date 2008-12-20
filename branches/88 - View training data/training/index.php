<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */
$cal = new Calendar(12,2008);

for($day = 1; $day <= $cal->getDaysInMonth();$day++){
	$timestamp = mktime(0,0,0,12,$day, 2008);
	$cal->addItemToDay($timestamp, "item $day");
}
$cal->addItemToDay(mktime(0,0,0,12,25, 2008), "CHRISTMAS");
$cal->addItemToDay(mktime(0,0,0,12,5, 2008), "sdasdsadsadasdsa dsadsadsadsadasdsds");

$smarty->assign("calendar", $cal);

$content = $smarty->fetch("training/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");


?>