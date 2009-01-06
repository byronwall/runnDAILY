<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */

$cal = new Calendar(mktime(), CAL_MONTH);

$training_items = TrainingLog::getItemsForUser($user->userID, $cal->getFirstDayOnCalendar(), $cal->getLastDayOnCalendar());

foreach($training_items as $item){
	$cal->addItemToDay(strtotime($item->date), $item);
}

$smarty->assign("calendar", $cal);

$content = $smarty->fetch("training/index.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");
?>