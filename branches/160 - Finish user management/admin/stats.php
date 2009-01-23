<?php
require("../lib/config.php");

$smarty->assign("stats",Stats::getRecentStats());
$smarty->display_master("admin/stats.tpl");
?>