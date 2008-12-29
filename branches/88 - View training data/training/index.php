<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */

$cal = new Calendar(mktime(), CAL_MONTH);
$cal2 = new Calendar(mktime(), CAL_WEEK);

$training_items = TrainingLog::getItemsForUser($user->userID, $cal->getFirstDayOnCalendar(), $cal->getLastDayOnCalendar());
$training_items2 = TrainingLog::getItemsForUser($user->userID, $cal2->getFirstDayOnCalendar(), $cal2->getLastDayOnCalendar());

foreach($training_items as $item){
	$cal->addItemToDay(strtotime($item->date), $item);
}
foreach($training_items2 as $item){
	$cal2->addItemToDay(strtotime($item->date), $item);
}

$smarty->assign("calendar", $cal);
$smarty->assign("calendar2", $cal2);

$content = $smarty->fetch("training/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");


?>