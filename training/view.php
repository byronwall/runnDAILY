<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */
if(!isset($_GET["tid"])){
	header("location: http://". $_SERVER["SERVER_NAME"]. "/training/" );
	exit;
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

$content = $smarty->fetch("training/view.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");


?>