<?php
require("../lib/config.php");

$smarty->assign("pages", Page::getAllPages());
$smarty->display_master("admin/pages.tpl");
?>