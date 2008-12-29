<?php
require("../lib/config.php");

/*
 * This is the index page for the training folder.
 */
$tid = (isset($_GET["tid"]))?$_GET["tid"]:0;

$training_item = TrainingLogItem::getItem($tid);
$smarty->assign("item", $training_item);

$content = $smarty->fetch("training/view.tpl");
$smarty->assign("page_content", $content);

$smarty->assign("page_title", "Training - Running Site");
$smarty->display("master.tpl");


?>