<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */

$cal = new Calendar(mktime(), CAL_MONTH);

$training_items = TrainingLog::getItemsForUser(User::$current_user->uid, $cal->getFirstDayOnCalendar(), $cal->getLastDayOnCalendar());

foreach($training_items as $item){
	$cal->addItemToDay($item->date, $item);
}

$smarty->assign("calendar", $cal);
$smarty->display_master("training/index.tpl");
?>