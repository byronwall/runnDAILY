<?php
require("../lib/config.php");

if(!isset($_GET["tid"])){
	Page::redirect("/training/");
}

$tid = $_GET["tid"];

$training_item = TrainingLog::getItem($tid);
$cal_week = new Calendar($training_item->date, CAL_WEEK);
$training_items = TrainingLog::getItemsForUser($training_item->uid, $cal_week->getFirstDayOnCalendar(), $cal_week->getLastDayOnCalendar());
foreach($training_items as $item){
	$cal_week->addItemToDay($item->date, $item);
}

$smarty->assign("item", $training_item);
$smarty->assign("calendar", $cal_week);

$smarty->display_master("training/view.tpl");
?>